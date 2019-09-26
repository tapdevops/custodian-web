START : 2018-09-27 14:49:44

                UPDATE TR_HO_SUMMARY_OUTLOOK
                SET
                    PERIOD_BUDGET = TO_DATE('2017', 'YYYY'),
                    CC_CODE = '015',
                    COA_CODE = '6201010401',
                    ACT_JAN = '0',
                    ACT_FEB = '0',
                    ACT_MAR = '0',
                    ACT_APR = '0',
                    ACT_MAY = '0',
                    ACT_JUN = '0',
                    ACT_JUL = '0',
                    ACT_AUG = '0',
                    OUTLOOK_SEP = '0',
                    OUTLOOK_OCT = '0',
                    OUTLOOK_NOV = '4000000',
                    OUTLOOK_DEC = '2000000',
                    ADJ = '0',
                    YTD_ACTUAL_ADJ = '0',
                    OUTLOOK = '6000000',
                    LATEST_PERIOD = '6000000',
                    ANNUALIZED_YTD = '0',
                    VARIANCE_RP = '6000000',
                    VARIANCE_PERSEN = '0',
                    UPDATE_USER = 'MUHAMMAD.RIZALDY',
                    UPDATE_TIME = CURRENT_TIMESTAMP
                WHERE
                    PERIOD_BUDGET = TO_DATE('2017', 'YYYY')
                    AND CC_CODE = '015' 
                    AND COA_CODE = '6201010401'
                ;
            COMMIT;
END : 2018-09-27 14:49:44
