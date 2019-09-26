START : 2018-08-30 22:26:03

                UPDATE TR_HO_SUMMARY_OUTLOOK
                SET
                    PERIOD_BUDGET = TO_DATE('2018', 'YYYY'),
                    CC_CODE = '015',
                    COA_CODE = '6201010401',
                    ACT_JAN = '0',
                    ACT_FEB = '0',
                    ACT_MAR = '0',
                    ACT_APR = '0',
                    ACT_MAY = '0',
                    ACT_JUN = '0',
                    ACT_JUL = '0',
                    ACT_AUG = '168000',
                    OUTLOOK_SEP = '0',
                    OUTLOOK_OCT = '0',
                    OUTLOOK_NOV = '0',
                    OUTLOOK_DEC = '0',
                    ADJ = '100000',
                    YTD_ACTUAL_ADJ = '268000',
                    OUTLOOK = '0',
                    LATEST_PERIOD = '268000',
                    ANNUALIZED_YTD = '402000',
                    VARIANCE_RP = '-84000',
                    VARIANCE_PERSEN = '-33.33',
                    UPDATE_USER = 'MUHAMMAD.RIZALDY',
                    UPDATE_TIME = CURRENT_TIMESTAMP
                WHERE
                    PERIOD_BUDGET = TO_DATE('2018', 'YYYY')
                    AND CC_CODE = '015' 
                    AND COA_CODE = '6201010401'
                ;
            COMMIT;
END : 2018-08-30 22:26:03
