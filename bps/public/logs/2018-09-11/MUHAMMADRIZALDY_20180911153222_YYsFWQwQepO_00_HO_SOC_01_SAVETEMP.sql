START : 2018-09-11 15:32:22

            UPDATE TR_HO_SUMMARY_OUTLOOK
            SET
                REMARKS = 'Susulan',
                UPDATE_USER = 'MUHAMMAD.RIZALDY',
                UPDATE_TIME = CURRENT_TIMESTAMP
            WHERE
                EXTRACT (YEAR FROM PERIOD_BUDGET) = '2018'
                AND CC_CODE = '045'
                AND COA_CODE = '6201011401';
        COMMIT;
END : 2018-09-11 15:32:22
