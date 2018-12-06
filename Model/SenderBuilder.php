<?php
namespace Ebizmarts\Mandrill\Model;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Mail\Template\TransportBuilderByStore;
use Magento\Sales\Model\Order\Email\Container\IdentityInterface;
use Magento\Sales\Model\Order\Email\Container\Template;
use Magento\Framework\Mail\MessageInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Mail\Template\SenderResolverInterface;

class SenderBuilder extends \Magento\Sales\Model\Order\Email\SenderBuilder
{
    /**
     * @var SenderResolverInterface
     */
    private $senderResolver;

    /**
     * SenderBuilder constructor.
     * @param Template $templateContainer
     * @param IdentityInterface $identityContainer
     * @param ObjectManagerInterface $objectManager
     * @param SenderResolverInterface $senderResolver
     * @param array $attachments
     */
    public function __construct(
        Template $templateContainer,
        IdentityInterface $identityContainer,
        ObjectManagerInterface $objectManager,
        SenderResolverInterface $senderResolver,
        array $attachments = []
    ) {
        /** @var MessageInterface $message */
        $message = $objectManager->create(MessageInterface::class);
        /** @var TransportBuilder $transportBuilder */
        $transportBuilder = $objectManager->create(
            TransportBuilder::class,
            ["message" => $message]
        );
        /** @var TransportBuilderByStore $transportBuilderByStore */
        $transportBuilderByStore = $objectManager->create(
            TransportBuilderByStore::class,
            ["message" => $message]
        );
        parent::__construct($templateContainer, $identityContainer, $transportBuilder, $transportBuilderByStore);
        $this->senderResolver = $senderResolver;
//        $transportBuilder->setAttachments($attachments);
    }
    protected function configureEmailTemplate()
    {
        parent::configureEmailTemplate();
        $this->transportBuilder->setFrom(
            $this->senderResolver->resolve(
                $this->identityContainer->getEmailIdentity(),
                $this->identityContainer->getStore()->getId()
            )
        );
    }
}
