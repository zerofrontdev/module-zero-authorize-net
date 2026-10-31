<?php
/**
 * Copyright © 2026 Roman Pasichnyk
 * SPDX-License-Identifier: OSL-3.0 OR AFL-3.0
 */

declare(strict_types=1);

namespace Zero\AuthorizeNet\Model;

use net\authorize\api\contract\v1\ANetApiResponseType;
use net\authorize\api\contract\v1\CreateTransactionRequest;
use net\authorize\api\contract\v1\GetTransactionDetailsRequest;
use net\authorize\api\contract\v1\GetTransactionDetailsResponse;
use net\authorize\api\contract\v1\UpdateHeldTransactionRequest;
use net\authorize\api\controller\CreateTransactionController;
use net\authorize\api\controller\GetTransactionDetailsController;
use net\authorize\api\controller\UpdateHeldTransactionController;

/**
 * Sends Authorize.Net requests (createTransactionRequest, getTransactionDetailsRequest,
 * updateHeldTransactionRequest),
 * behind one injectable class (so capture() and refund() can be unit-tested
 * without the network).
 */
class TransactionGateway
{
    /**
     * @param CreateTransactionRequest $request
     * @param string $environment ANetEnvironment::SANDBOX or ::PRODUCTION
     * @return ANetApiResponseType|null null when the API couldn't be reached
     */
    public function execute(CreateTransactionRequest $request, string $environment): ?ANetApiResponseType
    {
        return (new CreateTransactionController($request))->executeWithApiResponse($environment);
    }

    /**
     * A transaction's details (its masked card number, for refunds of payments made
     * before the last four digits were kept with the order).
     *
     * @param GetTransactionDetailsRequest $request
     * @param string $environment ANetEnvironment::SANDBOX or ::PRODUCTION
     * @return GetTransactionDetailsResponse|null null when the API couldn't be reached
     */
    public function details(GetTransactionDetailsRequest $request, string $environment): ?GetTransactionDetailsResponse
    {
        $response = (new GetTransactionDetailsController($request))->executeWithApiResponse($environment);

        return $response instanceof GetTransactionDetailsResponse ? $response : null;
    }

    /**
     * Approves or declines a transaction the Fraud Detection Suite held for review.
     *
     * @param UpdateHeldTransactionRequest $request action approve or decline, refTransId
     * @param string $environment ANetEnvironment::SANDBOX or ::PRODUCTION
     * @return ANetApiResponseType|null null when the API couldn't be reached
     */
    public function updateHeld(UpdateHeldTransactionRequest $request, string $environment): ?ANetApiResponseType
    {
        return (new UpdateHeldTransactionController($request))->executeWithApiResponse($environment);
    }
}
