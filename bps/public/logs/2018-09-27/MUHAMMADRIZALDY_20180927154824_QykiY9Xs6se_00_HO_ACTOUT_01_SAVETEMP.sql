START : 2018-09-27 15:48:24

                UPDATE TR_HO_SUMMARY_OUTLOOK
                SET
                    PERIOD_BUDGET = TO_DATE('2016', 'YYYY'),
                    CC_CODE = '015',
                    COA_CODE = '6201010401',
                    ACT_JAN = '0',
                    ACT_FEB = '0',
                    ACT_MAR = '0',
                    ACT_APR = '168000',
                    ACT_MAY = '0',
                    ACT_JUN = '0',
                    ACT_JUL = '0',
                    ACT_AUG = '2219340',
                    OUTLOOK_SEP = '0',
                    OUTLOOK_OCT = '0',
                    OUTLOOK_NOV = '5000000',
                    OUTLOOK_DEC = '0',
                    ADJ = '0',
                    YTD_ACTUAL_ADJ = '2387340',
                    OUTLOOK = '5000000',
                    LATEST_PERIOD = '7387340',
                    ANNUALIZED_YTD = '3581010',
                    VARIANCE_RP = '3806330',
                    VARIANCE_PERSEN = '106.29',
                    UPDATE_USER = 'MUHAMMAD.RIZALDY',
                    UPDATE_TIME = CURRENT_TIMESTAMP
                WHERE
                    PERIOD_BUDGET = TO_DATE('2016', 'YYYY')
                    AND CC_CODE = '015' 
                    AND COA_CODE = '6201010401'
                ;
            COMMIT;
END : 2018-09-27 15:48:24
