function validateForm() {
    const oilTypeRadios = document.querySelectorAll('input[name="oiltype"]');
    const amountFields = document.querySelectorAll('input[name="amount"]');
    
        oilTypeRadios.forEach((radio) => {
            radio.addEventListener('change', () => {
                const selectedOilType = radio.value;
                const amountField = document.querySelector(`#amount_${selectedOilType.replace(/\s+/g, '_')}`);
                amountFields.forEach((field) => {
                    field.value = '400'; // Set the default amount
                });
                amountField.value = '400'; // Set the selected oil type's amount
            });
        });
    }