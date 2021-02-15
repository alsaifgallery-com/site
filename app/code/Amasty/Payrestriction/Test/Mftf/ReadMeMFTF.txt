ReadeMeMFTF (It is recommendations for runs tests).

    Quantity all tests equals 29.

        For runs test, use group "Payrestriction".
            command: "vendor/bin/mftf run:group Payrestriction -r"


        Some tests (qty 7) check Payments Methods with credentials.
            For correct operation you have to fill you credentials in file "PaymentMethodsCredentialsData" in directory "Data".
            full path: "/magento/vendor/amasty/module-payrestriction/Test/Mftf/Data/PaymentMethodsCredentialsData"
