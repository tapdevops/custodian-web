START : 2018-08-30 21:38:53

                UPDATE TR_HO_REPORT_VALID 
                SET PERIOD_BUDGET = TO_DATE('2018', 'YYYY'),
                    DIV_CODE = 'D00003',
                    CC_CODE = '015',
                    GROUP_BUDGET = 'OPEX',
                    OUTLOOK = '200000000',
                    NEXT_BUDGET = '0',
                    VAR_SELISIH = '-200000000',
                    VAR_PERSEN = '0',
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
                    NEXT_BUDGET = '100000000',
                    VAR_SELISIH = '100000000',
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
                    OUTLOOK = '200000000',
                    NEXT_BUDGET = '100000000',
                    VAR_SELISIH = '-100000000',
                    VAR_PERSEN = '0',
                    INSERT_USER = 'MUHAMMAD.RIZALDY',
                    INSERT_TIME = SYSDATE
                WHERE PERIOD_BUDGET = TO_DATE('2018', 'YYYY') 
                    AND DIV_CODE = 'D00003'
                    AND CC_CODE = '015'
                    AND GROUP_BUDGET = 'TOTAL'
            COMMIT;
END : 2018-08-30 21:38:53
