<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\User\Communication\Form\Constraints;

use Generated\Shared\Transfer\UserConditionsTransfer;
use Generated\Shared\Transfer\UserCriteriaTransfer;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CurrentPasswordValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     * @param \Symfony\Component\Validator\Constraint|\Spryker\Zed\User\Communication\Form\Constraints\CurrentPassword $constraint
     *
     * @throws \Symfony\Component\Validator\Exception\UnexpectedTypeException
     *
     * @return void
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof CurrentPassword) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\Password');
        }

        if (!$this->isProvidedPasswordEqualsToPersisted($value, $constraint)) {
            $this->context->buildViolation($constraint->getMessage())
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->addViolation();
        }
    }

    /**
     * @param string $password
     * @param \Spryker\Zed\User\Communication\Form\Constraints\CurrentPassword $constraint
     *
     * @return bool
     */
    protected function isProvidedPasswordEqualsToPersisted($password, CurrentPassword $constraint): bool
    {
        $currentUserTransfer = $constraint->getFacadeUser()->getCurrentUser();

        $userCriteriaTransfer = (new UserCriteriaTransfer())->setUserConditions(
            (new UserConditionsTransfer())->addUsername($currentUserTransfer->getUsernameOrFail()),
        );
        $userCollectionTransfer = $constraint->getFacadeUser()->getUserCollection($userCriteriaTransfer);
        $freshUserTransfer = $userCollectionTransfer->getUsers()->getIterator()->current();

        return $constraint->getFacadeUser()
            ->isValidPassword($password, $freshUserTransfer->getPasswordOrFail());
    }
}
