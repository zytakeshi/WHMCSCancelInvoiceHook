# WHMCS Invoice Cancellation Feature

## Overview
This WHMCS module adds a cancel button to unpaid invoices, allowing clients to cancel invoices directly from the invoice view page.

## Features
- Add a cancel button to unpaid invoices
- Internationalization support
- Confirmation dialog before cancellation
- Logging of cancellation actions

## Requirements
- WHMCS 7.10 or higher
- PHP 7.2+

## Installation

### 1. Add Language Keys
Add the following language keys to your language files (e.g., `lang/english.php`):

```php
$_LANG['invoicecancellation'] = 'Cancel Invoice';
$_LANG['invoicecancelconfirmation'] = 'Are you sure you want to cancel this invoice? This action cannot be undone.';
$_LANG['invoicecancelsuccess'] = 'Your invoice has been successfully cancelled.';
$_LANG['invoicecancelerror'] = 'There was a problem cancelling your invoice. Please contact support.';
```

### 2. Install Hook File
Copy `invoice_cancel.php` to the `includes/hooks/` directory in your WHMCS installation.

### 3. Modify Template (Optional)
If you're using a custom template, ensure the following is added to your `viewinvoice.tpl`:

```smarty
{if $cancelButton}
    <div class="pull-right btn-group btn-group-sm hidden-print">
        <a href="javascript:window.print()" class="btn btn-default"><i class="fas fa-print"></i> {$LANG.print}</a>
        <a href="dl.php?type=i&amp;id={$invoiceid}" class="btn btn-default"><i class="fas fa-download"></i> {$LANG.invoicesdownload}</a>
        <div class="btn btn-default">{$cancelButton}</div>
    </div>
{/if}

{if $smarty.get.cancelled eq "1"}
    {include file="$template/includes/panel.tpl" type="success" headerTitle=$LANG.success bodyContent=$LANG.invoicecancelsuccess bodyTextCenter=true}
{elseif $smarty.get.cancelled eq "0"}
    {include file="$template/includes/panel.tpl" type="danger" headerTitle=$LANG.error bodyContent=$LANG.invoicecancelerror bodyTextCenter=true}
{/if}
```

## How It Works

### Cancellation Process
1. The hook adds a cancel button to unpaid invoices
2. Clicking the button shows a confirmation dialog
3. If confirmed, the invoice status is changed to 'Cancelled'
4. The action is logged in WHMCS activity log

### Cancellation Restrictions
- Only unpaid invoices can be cancelled
- Only the invoice owner can cancel their own invoice

## Customization

### Styling
You can modify the button style by changing the CSS classes in the hook file:
```php
$cancelButton = '<a href="' . $cancelUrl . '" class="btn btn-danger" style="margin-top: 10px;" onclick="return confirm(\'' . $_LANG['invoicecancelconfirmation'] . '\');">' . $_LANG['invoicecancellation'] . '</a>';
```

### Logging
The cancellation is logged using WHMCS's `logActivity()` function:
```php
logActivity("Invoice #$invoiceId cancelled by client", $userId);
```

## Troubleshooting
- Ensure the hook file is in the correct directory
- Verify language keys are correctly defined
- Check WHMCS error logs if cancellation fails

## Disclaimer
This module is provided as-is. Always backup your WHMCS installation before making changes. 
