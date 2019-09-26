START : 2018-09-27 15:44:13

                UPDATE TR_HO_SUMMARY_OUTLOOK
                SET
                    PERIOD_BUDGET = TO_DATE('2016', 'YYYY'),
                    CC_CODE = '015',
                    COA_CODE = '6201010601',
                    ACT_JAN = '199158750',
                    ACT_FEB = '222252500',
                    ACT_MAR = '221252500',
                    ACT_APR = '204577500',
                    ACT_MAY = '220252500',
                    ACT_JUN = '210700000',
                    ACT_JUL = '220177499',
                    ACT_AUG = '228862504',
                    OUTLOOK_SEP = '918750',
                    OUTLOOK_OCT = '918750',
                    OUTLOOK_NOV = '918750',
                    OUTLOOK_DEC = '918750',
                    ADJ = '0',
                    YTD_ACTUAL_ADJ = '1727233753',
                    OUTLOOK = '3675000',
                    LATEST_PERIOD = '1730908753',
                    ANNUALIZED_YTD = '2590850629.5',
                    VARIANCE_RP = '-859941876.5',
                    VARIANCE_PERSEN = '-33.19',
                    UPDATE_USER = 'MUHAMMAD.RIZALDY',
                    UPDATE_TIME = CURRENT_TIMESTAMP
                WHERE
                    PERIOD_BUDGET = TO_DATE('2016', 'YYYY')
                    AND CC_CODE = '015' 
                    AND COA_CODE = '6201010601'
                ;
            COMMIT;
END : 2018-09-27 15:44:13
