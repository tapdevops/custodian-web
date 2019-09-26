START : 2018-08-31 10:42:39

                UPDATE TR_HO_REPORT_VALID 
                SET PERIOD_BUDGET = TO_DATE('2018', 'YYYY'),
                    DIV_CODE = 'D00003',
                    CC_CODE = '015',
                    GROUP_BUDGET = 'OPEX',
                    OUTLOOK = '219066750',
                    NEXT_BUDGET = '97395994',
                    VAR_SELISIH = '-121670756',
                    VAR_PERSEN = '-55.54',
                    INSERT_USER = 'MUHAMMAD.RIZALDY',
                    INSERT_TIME = SYSDATE
                WHERE PERIOD_BUDGET = TO_DATE('2018', 'YYYY') 
                    AND DIV_CODE = 'D00003'
                    AND CC_CODE = '015'
                    AND GROUP_BUDGET = 'OPEX'
            
                UPDATE TR_HO_REPORT_VALID 
                SET PERIOD_BUDGET = TO_DATE('2018', 'YYYY'),
                    DIV_CODE = 'D00003',
                    CC_CODE = '015',
                    GROUP_BUDGET = 'CAPEX',
                    OUTLOOK = '0',
                    NEXT_BUDGET = '101395994',
                    VAR_SELISIH = '101395994',
                    VAR_PERSEN = '0',
                    INSERT_USER = 'MUHAMMAD.RIZALDY',
                    INSERT_TIME = SYSDATE
                WHERE PERIOD_BUDGET = TO_DATE('2018', 'YYYY') 
                    AND DIV_CODE = 'D00003'
                    AND CC_CODE = '015'
                    AND GROUP_BUDGET = 'CAPEX'
            
                UPDATE TR_HO_REPORT_VALID 
                SET PERIOD_BUDGET = TO_DATE('2018', 'YYYY'),
                    DIV_CODE = 'D00003',
                    CC_CODE = '015',
                    GROUP_BUDGET = 'TOTAL',
                    OUTLOOK = '219066750',
                    NEXT_BUDGET = '198791988',
                    VAR_SELISIH = '-20274762',
                    VAR_PERSEN = '-55.54',
                    INSERT_USER = 'MUHAMMAD.RIZALDY',
                    INSERT_TIME = SYSDATE
                WHERE PERIOD_BUDGET = TO_DATE('2018', 'YYYY') 
                    AND DIV_CODE = 'D00003'
                    AND CC_CODE = '015'
                    AND GROUP_BUDGET = 'TOTAL'
            COMMIT;
END : 2018-08-31 10:42:39
