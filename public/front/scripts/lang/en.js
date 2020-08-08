//多语言配置
var lanConfig = {
    'text': {
        'ok': 'Ok',
        'cancel': 'Cancel',
        'qty': 'Qty',
        'size': 'Size',
        'style': 'Style',
        'price': 'Price',
        'checkout': 'Check Out',
        'your_order': 'Your Order',
        'shipping_company': 'Shipping Company',
        'estimated_delivery_time' : 'Estimated Delivery Time',
        'shipping_cost': 'Shipping Cost',
        'campaigns': 'Campaigns',
        'storefronts': 'Storefronts',
        'orders': 'Orders',
        'messages': 'Messages',
        'payouts': 'Payouts',
        'settings': 'Settings',
        'log_out': 'Log Out',
        'save': "Save"
    },
    'valid': {
        'required_email': 'Please input the Email',
        'required_password': 'Please input the Password',
        'valid_phone': 'Please enter a valid phone number',
        'required_first_name': 'Please input the First Name',
        'required_last_name': 'Please input the Last Name',
        'required_address1': 'Please input the Address Line',
        'required_city': 'Please input the City',
        'required_province': 'Please input the Province',
        'required_country': 'Please input the Country',
        'required_zip': 'Please input the Postal Code',
        'required_phone': 'Please input the Phone Number',
        'required_security_code': 'Please input the Security Code',
        'required_paypal_email': 'Please input the Payout email',
        'required_currency': 'Please input the Payout currency',
        'required_verificate_code': 'Please input the Security Code',
        'valid_letters_number': 'Please input the characters with letters or numbers or underline',
        'required_title': 'Please input the Title',
        'required_url': 'Please input the Url',
        'required_public_name': 'Please input the Public Name',
        'required_current_pwd': 'Please input the Current Password',
        'required_new_password': 'Please input the New Password'
    },
    'common': {
        'requestError': "Oh~ damn it,Please be patient and I'm trying to speed up.",
        'noListData': 'Looks like can not find any data',
        'pleaseSelect': 'Please Select',
        'large_order_qty': 'Looking to place a large order?Please contact us',
        'request_wait': 'The server is being processed, please wait later!',
        'upload_file_tip': 'No,please check it,you can only upload picture',
        'filter_search_placeholder': 'Filter by search'
    },
    'address': {
        'shipping_list_note': 'Please note:The shipping cost on this page is just an estimated value, please take the one on the order page as a standard.',
        'no_country_shipping_methods': 'Sorry, can no find any shipping methods for this country.'
    },
    'checkout': {
        'check_checkout_info': 'Please check your checkout information!',
        'cannot_shipped_address': 'Sorry, we can not be shipped to this address, please input another address',
        'not_country_payment_method': 'Sorry, not any payment method support for your country.'
    },
    'order': {
        'cancel_tip': 'Are your sure to cancel this order?'
    },
    'campaign': {
        'add_style_color': 'Add another style or color',
        'reached_minimum_goal': 'Unfortunately, your goal has reached the minimum 1',
        'below_current_goal': 'Unfortunately, your new goal must be below current goal',
        'campaign_archived_tip': 'Are your sure to archived it ?',
    },
    'message': {
        'remove_group': 'Are you sure to remove this group?',
        'success_sent': 'We are attempting to send your email. If we encounter any issues, you will receive an email informing you of them.'
    },
    'pauout': {
        'received_title': 'Woohoo ! your request was received !'
    } 
};
//验证提示多语言设置
lanConfig.validatorMessage = function(){
    $.extend($.validator.messages, {
        required: "This field is required.",
        remote: "Please fix this field.",
        email: "Please enter a valid email address.",
        url: "Please enter a valid Url.",
        date: "Please enter a valid date.",
        dateISO: "Please enter a valid date (ISO).",
        number: "Please enter a valid number.",
        digits: "Please enter only digits.",
        creditcard: "Please enter a valid credit card number.",
        equalTo: "Please enter the same value again.",
        maxlength: $.validator.format("Please enter no more than {0} characters."),
        minlength: $.validator.format("Please enter at least {0} characters."),
        rangelength: $.validator.format("Please enter a value between {0} and {1} characters long."),
        range: $.validator.format("Please enter a value between {0} and {1}."),
        max: $.validator.format("Please enter a value less than or equal to {0}."),
        min: $.validator.format("Please enter a value greater than or equal to {0}.")
    });
};
 
