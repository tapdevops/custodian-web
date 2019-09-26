START : 2018-08-30 21:31:33

            UPDATE TR_HO_SUMMARY_OUTLOOK
            SET
                REMARKS = 'Tambahan Tenaga Ahli',
                UPDATE_USER = 'MUHAMMAD.RIZALDY',
                UPDATE_TIME = CURRENT_TIMESTAMP
            WHERE
                EXTRACT (YEAR FROM PERIOD_BUDGET) = '2018'
                AND CC_CODE = '015'
                AND COA_CODE = '6201010603';
        COMMIT;
END : 2018-08-30 21:31:33
