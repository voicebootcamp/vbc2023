(function (document, Joomla) {
    const appId = Joomla.getOptions('squareAppId');
    const locationId = Joomla.getOptions('squareLocationId');

    async function initializeCard(payments) {
        const card = await payments.card();
        await card.attach('#square-card-element');

        return card;
    }

    async function tokenize(paymentMethod) {
        const tokenResult = await paymentMethod.tokenize();
        if (tokenResult.status === 'OK') {
            return tokenResult.token;
        } else {
            throw new Error(
                `Tokenization errors: ${JSON.stringify(tokenResult.errors)}`
            );
        }
    }

    async function verifyBuyer(payments, token) {
        const form = document.getElementById('os_form');
        let verificationDetails = {};
        let billingContact = {};
        let addressLines = [];
        let firstName = getFormInputValue(form.first_name);
        let lastName = getFormInputValue(form.last_name);
        let email = getFormInputValue(form.email);
        let phone = getFormInputValue(form.phone);
        let address = getFormInputValue(form.address);
        let address2 = getFormInputValue(form.address2);
        let city = getFormInputValue(form.city);
        verificationDetails.amount = getFormInputValue(form.querySelector('#gross_amount'));

        if (verificationDetails.amount === '') {
            verificationDetails.amount = getFormInputValue(form.querySelector('#amount'));
        }
        
        verificationDetails.intent = 'CHARGE';
        verificationDetails.currencyCode = Joomla.getOptions('currencyCode', 'GBP');

        if (address.length) {
            addressLines.push(address);
        }

        if (address2.length) {
            addressLines.push(address2);
        }

        if (addressLines.length) {
            billingContact.addressLines = addressLines;
        }

        if (firstName) {
            billingContact.familyName = firstName;
        }

        if (lastName) {
            billingContact.givenName = lastName;
        }

        billingContact.email = email;

        if (phone) {
            billingContact.phone = phone;
        }

        if (city) {
            billingContact.city = city;
        }

        verificationDetails.billingContact = billingContact;

        const verificationResults = await payments.verifyBuyer(
            token,
            verificationDetails
        );

        return verificationResults.token;
    }

    let squareCard, payments;

    createSquareCardElement = async function () {
        try {
            payments = window.Square.payments(appId, locationId);
        } catch (e) {
            console.error('Initializing Payments failed', e);
        }

        try {
            squareCard = await initializeCard(payments);
        } catch (e) {
            console.error('Initializing Card failed', e);
        }
    };

    squareCardCallBackHandle = async function () {
        try {
            const form = document.getElementById('os_form');
            const token = await tokenize(squareCard);

            let verificationToken = await verifyBuyer(payments, token);

            form.square_card_token.value = token;
            form.square_card_verification_token.value = verificationToken;
            form.submit();
        } catch (e) {
            console.log(e);
            alert(e.message);
            document.getElementById('btn-submit').disabled = false;
        }
    };

    function getFormInputValue(input, defaultValue = '') {
        return input ? input.value : defaultValue;
    }

})(document, Joomla);