# Changelog: zerofront/module-zero-authorize-net

## 1.3.6

- A transaction held for review by Authorize.Net's Fraud Detection Suite puts the order in Payment Review (status Suspected Fraud); Accept and Deny on the order approve or decline it on Authorize.Net.
- A declined transaction or an error refuses the order with Authorize.Net's reason ("Your card payment could not be completed: …").
- Refunds carry a key derived from the credit memo.

Earlier releases (1.3.5 and before): see the Git tags.
