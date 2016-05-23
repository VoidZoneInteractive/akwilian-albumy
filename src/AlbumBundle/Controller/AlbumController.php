<?php

namespace AlbumBundle\Controller;

use AlbumBundle\Entity\Album;
use AlbumBundle\Form\AlbumType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AlbumController extends Controller {

    public function showAction()
    {

    }

    /**
     * @param Request $request
     * @return array
     */
    public function orderFormAction(Request $request)
    {
        $entity = new Album();

        $form = $this->createForm(
            AlbumType::class,
            $entity
        );

        return $this->render(
            'AlbumBundle:Album:order-form.html.twig',
            array(
                'entity' => $entity,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Creates a new Voucher entity.
     *
     * @Route("/create", name="cms_voucher_create")
     * @Method("POST")
     * @Template("WegeCmsBundle:Voucher:new.html.twig")
     * @param Request $request
     * @return array|RedirectResponse
     * @throws \Exception
     */
    public function createAction(Request $request)
    {
        SecurityController::checkPermission($this, "CONTENT.VOUCHER_EDIT");
        $entity = new Voucher();
        $pem = $this->getProjectEntityManager();
        $options = array();
        $options['em'] = $pem;
        //load from formdata before bind and set to entitymanger in formtype --> required because of manipulation of the entity fieldtype in form with ajax
        $form_data = $request->request->get('wege_cmsbundle_vouchertype');
        $voucher_source_id = array_key_exists('voucher_source', $form_data) ? $form_data['voucher_source'] : null;
        // If the VS is AffiliateNetwork the user must have the correct permission!
        if ($voucher_source_id == $this->getAffiliateVSId()) {
            SecurityController::checkPermission($this, 'CONTENT.VOUCHER_SELECT_VS_AFFILIATE');
        }
        // Same goes for the homepage VS
        if ($voucher_source_id == $this->getHomepageVSId()) {
            SecurityController::checkPermission($this, 'CONTENT.VOUCHER_SELECT_VS_HOMEPAGE');
        }
        // Get the affiliate voucher
        $affiliate_voucher_id = $request->get('affiliate_voucher');
        if ($affiliate_voucher_id !== null) {
            SecurityController::checkPermission($this, "CONTENT.AFFILIATE_VOUCHER_EDIT");
            // If the user comes from an affiliate feed, completely ignore whatever the voucher_source may be - it is "Affiliate_Network"
            $voucher_source_id = $this->getAffiliateVSId();
            $options['vs_remove'] = true;
        }
        if ($voucher_source_id) {
            $options['vs_id'] = $voucher_source_id;
        }
        $form = $this->createForm(
            new VoucherType(
                $options,
                $this->getUser(),
                SecurityController::getPermissionObject($this),
                $this->getCurrentProjectPrefix(),
                $this->get('translator'),
                $this->getCurrentProject()->getLanguage(),
                false,
                true,
                $this->getProjectTimeZone(),
                false
            ),
            $entity
        );
        //do first for correct trigger history
        $entity->setLastUpdatedBy($this->getUser()->getUsername());
        $entity->setInsertUserId($this->getUser()->getId());
        $form->submit($request);
        // The vouchersource entity won't be added if it was not submitted
        // It must be added manually
        if ($voucher_source_id && $entity->getVoucherSource() === null) {
            $entity->setVoucherSource($pem->getRepository('WegeCmsBundle:VoucherSource')->find($voucher_source_id));
        }
        // Set the entity type
        self::setEntityType($entity, $request);
        $page_coming_from = $form_data['page'];
        if ($form->isValid() && $this->customValidate($entity, $form, $voucher_source_id)) {
            // Publish the entity by default - may be changed by the voucher source action
            $entity->setPublished(true);
            $shop = $entity->getShop();
            // If voucher is exclusive then set it as tip
            if ($entity->getExclusive()) {
                $entity->setTip(true);
            }
            // Set EffectiveFrom to current datetime if empty
            if (is_null($entity->getEffectivefrom())) {
                //removeing the seconds of the current DateTime
                $d = new \DateTime();
                $datestr = $d->format('Y-m-d H:i');
                $entity->setEffectivefrom(new \DateTime($datestr));
            }
            // In case the ExpiredDate is lower than the current date set as expired
            $this->checkVoucherExpiredDate($entity);
            //the rules for moderation and publish
            if ($entity->getShop()->getModerate() == true) {
                $entity->setModerate(true);
            } else {
                /*
                 * Try to catch the mysterious error which occurs sometimes, but can't be reproduced later.
                 */
                $tmp_shop = $entity->getShop();
                $tmp_vouchersource_template = null;
                $tmp_shop_id = "NIX";
                $tmp_vouchersource_template_id = "NIX";
                if (false === empty($tmp_shop)) {
                    $tmp_shop_id = $tmp_shop->getId();
                    $tmp_vouchersource_template = $tmp_shop->getVoucherSourceTemplate();
                }
                if (empty($tmp_shop) || empty($tmp_vouchersource_template)) {
                    $tmp_id = $entity->getId();
                    if (empty($tmp_id)) {
                        $tmp_id = "NIX";
                    }
                    $tmp_vouchersource = $entity->getVoucherSource();
                    $tmp_vouchersource_id = "NIX";
                    if (false === empty($tmp_vouchersource)) {
                        $tmp_vouchersource_id = $tmp_vouchersource->getId();
                    }
                    // Can never happen. Egal...
                    if (false === empty($tmp_vouchersource_template)) {
                        $tmp_vouchersource_template_id = $tmp_vouchersource_template->getId();
                    }
                    $logger = $this->container->get('logger');
                    $logger->critical(
                        "MYSTERIOUS Save Error: VID = {entity_id}, SID = {shop_id}, VSTID = {vst_id}",
                        array(
                            'entity_id' => $tmp_id,
                            'shop_id' => $tmp_shop_id,
                            'vs_id' => $tmp_vouchersource_id,
                            'vst_id' => $tmp_vouchersource_template_id
                        )
                    );
                }
                //load voucherSource with voucherSourceEntry for shop an voucherSource id
                $vouchersource = VoucherSourceController::getVoucherSource(
                    $pem,
                    $entity->getShop()->getId(),
                    $entity->getShop()->getVoucherSourceTemplate()->getId(),
                    $entity->getVoucherSource()->getId()
                );
                if ($vouchersource->getAction() == VoucherSource::ACTION_MODERATE) {
                    $entity->setModerate(true);
                } else {
                    if ($vouchersource->getAction() == VoucherSource::ACTION_PUBLISH) {
                        $entity->setModerate(false);
                        $entity->setPublished(true);
                    } else { // ACTION_UNPUBLISH, ACTION_FROM_TEMPLATE
                        $entity->setModerate(false);
                        $entity->setPublished(false);
                    }
                }
            }
            //set the vouchersource (without changes for a shop to the entitiy --> for the link to the template)
            $voucher_source = $pem->getRepository('WegeCmsBundle:VoucherSource')->find($voucher_source_id);
            $entity->setVoucherSource($voucher_source);
            //set top voucher if shop is_top
            $entity->setIsTop($entity->getShop()->getIsTop());
            //set the Value
            $entity = ValuePatternController::setValueForVoucher(
                $entity,
                $this->getCurrentProject(),
                $this->getDoctrine()->getManager(),
                $this->getUser()->getUsername()
            );
            $shop->setModified(time());
            /**
             * Attention: This has to be the last check that influences the checking and published flags
             */
            if ($this->isUserAllowedToPublishVoucher($this) === false) {
                $entity->setChecking(true);
            }
            // if the voucher is not already forced
            if ($entity->getIsForced() == false) {
                // once the voucher attributes moderate and status cannot be changed anymore check if the voucher
                // muss be forced publish ts < current ts, moderate == false and published == true
                $entity->setIsForced($this->forceVoucherSubscribers($entity, $shop));
            }
            $pem->persist($entity);
            $pem->persist($shop);
            // In case BCP exists, we'll update his status as ACCEPTED
            if(!empty($form_data['bcp_vid'])){
                $bcp_voucher= $pem->getRepository('WegeCmsBundle:BCPVoucher')->find($form_data['bcp_vid']);
                $bcp_voucher->setStatus(BCPVoucher::STATUS_ACCEPTED);
                $bcp_voucher->setVoucher($entity);
                $pem->persist($bcp_voucher);
            }
            // Update the affiliate voucher
            if ($affiliate_voucher_id != null) {
                $affiliate_voucher = $pem->getRepository('WebgearsAffiliateBundle:AffiliateVoucher')->find(
                    $affiliate_voucher_id
                );
                $affiliate_voucher->setChanges(null);
                $affiliate_voucher->setVoucher($entity);
                $affiliate_voucher->setProcessed(true);
                $pem->persist($affiliate_voucher);
                // Create a new revision entity
                $revision = new Revision();
                // Todo: Use when PHP 5.5: $revision->setObjectType(AffiliateVoucher::class);
                $revision->setObjectType(AffiliateVoucher::class_descriptor);
                $revision->setObjectId($affiliate_voucher->getId());
                $revision->setUser($this->getUser()->getId());
                $revision->setActionTs(new \DateTime());
                $pem->persist($revision);
                // Create a shop mapping if not existing!
                $shop_mapping = $this->updateShopMapping($affiliate_voucher, $entity->getShop());
                $pem->persist($shop_mapping);
            }
            $pem->flush();
            // add tags after flush to get the voucher id
            /** @var Tag[] $tags */
            $tags = $form->get('tags')->getData();
            foreach ($tags as $tag) {
                $tag->addVoucher($entity);
                $pem->persist($tag);
            }
            // flush to save tags
            $pem->flush();
            //flush secondtime for generating hash for gutscheino --> postPersist Method
            if ($this->getCurrentProjectPrefix() == 'go') {
                $pem->flush();
            }
            $pem->clear();
            if ($entity->getCodeFile() != null) {
                //handle code file upload.
                //need to reload voucher --> because of flush and clear em in handle codes
                $this->handleCodeFile($entity->getCodeFile(), $entity->getId(), $pem);
                $voucher = $pem->getRepository('WegeCmsBundle:Voucher')->find($entity->getId());
                $voucher->setHasVouchercodes(true);
                $pem->persist($voucher);
                $pem->flush();
                $pem->clear();
            }
            //create Voucher Mail Alert, a mail alert is created just if a new voucher is created
            //in case of update it should not be created
            $voucherMailAlertEntity = new VoucherMailAlert();
            $voucherMailAlertEntity->setVid($entity->getId());
            $voucherMailAlertEntity->setTsCreated($entity->getEffectivefrom()->getTimestamp());
            $pem->persist($voucherMailAlertEntity);
            $pem->flush();
            $pem->clear();
            //create voucher clicks entity if the position has been set
            $prev_voucher_id = $form->get('voucher_prev_id')->getData();
            if ($prev_voucher_id) {
                $prev_voucher_id = str_replace('vid-', '', $prev_voucher_id);
                //load clicks summary of the reference voucher
                $prev_voucher_clicks = $pem->getRepository('WegeCmsBundle:ClicksSummaryVoucher')->findOneBy(
                    array('object_id' => $prev_voucher_id)
                );
                $voucher_clicks = new ClicksSummaryVoucher();
                $voucher_clicks->setObjectId($entity->getId());
                if ($prev_voucher_clicks) {
                    $voucher_clicks->setOffset24h($prev_voucher_clicks->getSum24h() + 10);
                    $voucher_clicks->setOffset3d($prev_voucher_clicks->getSum3d() + 10);
                    $voucher_clicks->setOffset7d($prev_voucher_clicks->getSum7d() + 10);
                    $voucher_clicks->setOffset30d($prev_voucher_clicks->getSum30d() + 10);
                    $voucher_clicks->setOffsetAlltime($prev_voucher_clicks->getSumAllTime() + 10);
                } else {
                    $voucher_clicks->setOffset24h(10);
                    $voucher_clicks->setOffset3d(10);
                    $voucher_clicks->setOffset7d(10);
                    $voucher_clicks->setOffset30d(10);
                    $voucher_clicks->setOffsetAlltime(10);
                }
                $voucher_clicks->setSums();
                $pem->persist($voucher_clicks);
                $pem->flush();
            }
            // Notify all listeners that a new voucher has been added
            $event = new VoucherEvent(
                $entity,
                array('external_voucher_entry' => $form->get('external_voucher_entry')->getData())
            );
            $this->get('event_dispatcher')->dispatch(VoucherEvents::VOUCHER_CREATE, $event);
            $create_more = $form->get('create_more')->getData();
            if ($create_more) {
                if ($form->get('external_voucher_entry')->getData() !== null) {
                    return $this->redirect(
                        $this->generateUrl(
                            'cms_voucher_new',
                            array(
                                'source' => ExternVoucherEntryController::SOURCE_SUBMIT,
                                'eid' => $form->get('external_voucher_entry')->getData()
                            )
                        )
                    );
                }
                return $this->redirect($this->generateUrl('cms_voucher_new'));
            }
            // Return to affiliate page if saving an affiliate voucher
            if ($affiliate_voucher_id !== null) {
                return $this->redirect($this->generateUrl('affiliate_feed_view', array('page' => $page_coming_from)));
            }
            if ($form->get('external_voucher_entry')->getData() !== null) {
                $external_voucher_entry = $pem->getRepository('WebgearsVoucherSubmitBundle:ExternVoucherEntry')
                    ->find($form->get('external_voucher_entry')->getData());
                return $this->redirect($this->generateUrl(
                    'wege_cms_submit_index',
                    array('source' => $external_voucher_entry->getSource())
                ));
            }
            // Return to destination if given
            $destination = $request->get('destination');
            if (!empty($destination) && $destination === 'vouchers_moderate') {
                return $this->redirect($this->generateUrl('cms_voucher_moderate'));
            }
            // Return to page stored in session if given
            if ($url = $this->get('wege_cms.voucher.helper')->getRedirectAfterAddOrEdit(array('id' => $entity->getId()))) {
                return $this->redirect($url);
            }
            return $this->redirect($this->generateUrl('cms_voucher', array('id' => $entity->getId())));
        }
        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'type' => $entity->getType(),
            'affiliate_voucher' => $affiliate_voucher_id
        ) + $this->generateSiteMetaData();
    }
} 