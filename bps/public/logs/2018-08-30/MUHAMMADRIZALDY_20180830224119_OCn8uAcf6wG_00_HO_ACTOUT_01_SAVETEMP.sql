START : 2018-08-30 22:41:20

                UPDATE TR_HO_SUMMARY_OUTLOOK
                SET
                    PERIOD_BUDGET = TO_DATE('2018', 'YYYY'),
                    CC_CODE = '015',
                    COA_CODE = '6201010603',
                    ACT_JAN = '0',
                    ACT_FEB = '0',
                    ACT_MAR = '0',
                    ACT_APR = '0',
                    ACT_MAY = '0',
                    ACT_JUN = '249375',
                    ACT_JUL = '249375',
                    ACT_AUG = '0',
                    OUTLOOK_SEP = '0',
                    OUTLOOK_OCT = '0',
                    OUTLOOK_NOV = '0',
                    OUTLOOK_DEC = '0',
                    ADJ = '0',
                    YTD_ACTUAL_ADJ = '498750',
                    OUTLOOK = '0',
                    LATEST_PERIOD = '498750',
                    ANNUALIZED_YTD = '748125',
                    VARIANCE_RP = '-249375',
                    VARIANCE_PERSEN = '-33.33',
                    UPDATE_USER = 'MUHAMMAD.RIZALDY',
                    UPDATE_TIME = CURRENT_TIMESTAMP
                WHERE
                    PERIOD_BUDGET = TO_DATE('2018', 'YYYY')
                    AND CC_CODE = '015' 
                    AND COA_CODE = '6201010603'
                ;
            
                UPDATE TR_HO_SUMMARY_OUTLOOK
                SET
                    PERIOD_BUDGET = TO_DATE('2018', 'YYYY'),
                    CC_CODE = '015',
                    COA_CODE = '6201010601',
                    ACT_JAN = '8750000',
                    ACT_FEB = '8750000',
                    ACT_MAR = '0',
                    ACT_APR = '0',
                    ACT_MAY = '0',
                    ACT_JUN = '0',
                    ACT_JUL = '0',
                    ACT_AUG = '0',
                    OUTLOOK_SEP = '0',
                    OUTLOOK_OCT = '0',
                    OUTLOOK_NOV = '0',
                    OUTLOOK_DEC = '200000000',
                    ADJ = '0',
                    YTD_ACTUAL_ADJ = '17500000',
                    OUTLOOK = '200000000',
                    LATEST_PERIOD = '217500000',
                    ANNUALIZED_YTD = '26250000',
                    VARIANCE_RP = '191250000',
                    VARIANCE_PERSEN = '728.57',
                    UPDATE_USER = 'MUHAMMAD.RIZALDY',
                    UPDATE_TIME = CURRENT_TIMESTAMP
                WHERE
                    PERIOD_BUDGET = TO_DATE('2018', 'YYYY')
                    AND CC_CODE = '015' 
                    AND COA_CODE = '6201010601'
                ;
            COMMIT;
END : 2018-08-30 22:41:20
