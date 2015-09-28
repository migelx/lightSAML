<?php

namespace LightSaml\Action;

use LightSaml\Context\AbstractContext;
use LightSaml\Context\Profile\ExceptionContext;
use LightSaml\Context\Profile\ProfileContexts;

class CatchableErrorAction implements ActionInterface
{
    /** @var  ActionInterface */
    protected $mainAction;

    /** @var  ActionInterface */
    protected $errorAction;

    /**
     * @param ActionInterface $mainAction
     * @param ActionInterface $errorAction
     */
    public function __construct(ActionInterface $mainAction, ActionInterface $errorAction)
    {
        $this->mainAction = $mainAction;
        $this->errorAction = $errorAction;
    }

    /**
     * @param AbstractContext $context
     *
     * @return void
     */
    public function execute(AbstractContext $context)
    {
        try {
            $this->mainAction->execute($context);
        } catch (\Exception $ex) {
            /** @var ExceptionContext $exceptionContext */
            $exceptionContext = $context->getSubContext(ProfileContexts::EXCEPTION, ExceptionContext::class);
            $exceptionContext->addException($ex);

            $this->errorAction->execute($context);
        }
    }
}