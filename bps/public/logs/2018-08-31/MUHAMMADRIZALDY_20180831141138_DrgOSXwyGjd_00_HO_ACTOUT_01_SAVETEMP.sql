START : 2018-08-31 14:11:39

                UPDATE TR_HO_SUMMARY_OUTLOOK
                SET
                    PERIOD_BUDGET = TO_DATE('2018', 'YYYY'),
                    CC_CODE = '009',
                    COA_CODE = '6201011803',
                    ACT_JAN = '2049900',
                    ACT_FEB = '2049900',
                    ACT_MAR = '0',
                    ACT_APR = '0',
                    ACT_MAY = '0',
                    ACT_JUN = '0',
                    ACT_JUL = '0',
                    ACT_AUG = '0',
                    OUTLOOK_SEP = '0',
                    OUTLOOK_OCT = '0',
                    OUTLOOK_NOV = '0',
                    OUTLOOK_DEC = '12000000',
                    ADJ = '-2049900',
                    YTD_ACTUAL_ADJ = '2049900',
                    OUTLOOK = '12000000',
                    LATEST_PERIOD = '14049900',
                    ANNUALIZED_YTD = '3074850',
                    VARIANCE_RP = '9950100',
                    VARIANCE_PERSEN = '161.8',
                    UPDATE_USER = 'MUHAMMAD.RIZALDY',
                    UPDATE_TIME = CURRENT_TIMESTAMP
                WHERE
                    PERIOD_BUDGET = TO_DATE('2018', 'YYYY')
                    AND CC_CODE = '009' 
                    AND COA_CODE = '6201011803'
                ;
            COMMIT;
END : 2018-08-31 14:11:39
