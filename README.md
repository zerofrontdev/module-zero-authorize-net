# Zero_AuthorizeNet

Card payments through Authorize.Net for Zero Checkout (`zero_authorizenet`).
Accept.js turns the card fields into a short-lived token in the browser; the
charge (`authCaptureTransaction`) is made on the server when the order is
placed, for the order's base grand total. Uses the official
`authorizenet/authorizenet` SDK.

Settings: Stores > Configuration > Sales > Payment Methods > **Credit Card
(Authorize.Net)**: Enabled, Title, Test Mode, the sandbox and live API Login
ID, Transaction Key and Client Key.

## Install

Requires Magento Open Source 2.4.7–2.4.9 and PHP 8.2–8.4 with the GD extension built with WebP (`php -r 'var_dump(function_exists("imagewebp"));'` says `true`; Magento's own PHP requirements cover the rest). Without WebP in GD, Zero skips its WebP image copies and serves the originals.

```bash
composer require zerofront/module-zero-authorize-net
bin/magento setup:upgrade
bin/magento setup:di:compile           # production mode
bin/magento setup:static-content:deploy # production mode
bin/magento cache:flush
```

Composer installs `zerofront/module-zero-checkout` with it.

Part of [`zerofront/zero`](https://zerofront.dev), which installs the Zero theme and every Zero module at once.

## What the answer does to the order

The transaction's `responseCode`:

| Code | Meaning | The order |
| --- | --- | --- |
| 1 | Approved | Paid and invoiced |
| 4 | Held for review (Fraud Detection Suite filters) | **Payment Review**, status Suspected Fraud, the invoice open; additional information `authnet_held_for_review` = 1 |
| 2, 3, other | Declined, error | Refused: the customer sees Authorize.Net's reason and no order is made |

An error on the transaction response refuses the order whatever the code.

**Payment Review.** On the order, **Accept Payment** approves the held
transaction on Authorize.Net (`updateHeldTransactionRequest`, action
`approve`) and Magento pays the invoice; **Deny Payment** declines it
(`decline`) and Magento cancels the invoice and the order. When
Authorize.Net refuses either, the admin sees its message and the order stays
in review. Authorize.Net only holds transactions when a fraud filter in the
merchant interface is set to "hold for review", so a store without those
filters never sees this.

## Refunds

Credit memos refund the card (`refundTransaction`, by the card's last four
digits). A payment Authorize.Net hasn't settled yet (it settles daily) can
only be voided whole, so a partial refund is refused until it settles.

## Not confirmed in a sandbox

Response codes 1-4 and code 4 arriving without errors are from Authorize.Net's
API reference; Accept and Deny of a held transaction need a sandbox with a
fraud filter on "hold for review" and have not been run against Authorize.Net
yet.

What changed in each release: [CHANGELOG.md](CHANGELOG.md).

## License

OSL-3.0 or AFL-3.0, your choice: see `NOTICE.md`.

---

Zero is made by [Fixstacks](https://fixstacks.com).
Documentation: https://zerofront.dev/docs. Demo store: https://demo.zerofront.dev.
