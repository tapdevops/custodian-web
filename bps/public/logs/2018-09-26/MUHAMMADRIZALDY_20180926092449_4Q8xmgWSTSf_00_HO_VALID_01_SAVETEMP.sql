START : 2018-09-26 09:24:49

                UPDATE TR_HO_REPORT_VALID 
                SET PERIOD_BUDGET = TO_DATE('2018', 'YYYY'),
                    DIV_CODE = 'D00003',
                    CC_CODE = '015',
                    GROUP_BUDGET = 'OPEX',
                    OUTLOOK = '8678042601',
                    NEXT_BUDGET = '138838297',
                    VAR_SELISIH = '-8539204304',
                    VAR_PERSEN = '-98.4',
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
                    NEXT_BUDGET = '182838297',
                    VAR_SELISIH = '182838297',
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
                    OUTLOOK = '8678042601',
                    NEXT_BUDGET = '321676594',
                    VAR_SELISIH = '-8356366007',
                    VAR_PERSEN = '-98.4',
                    INSERT_USER = 'MUHAMMAD.RIZALDY',
                    INSERT_TIME = SYSDATE
                WHERE PERIOD_BUDGET = TO_DATE('2018', 'YYYY') 
                    AND DIV_CODE = 'D00003'
                    AND CC_CODE = '015'
                    AND GROUP_BUDGET = 'TOTAL'
            COMMIT;
END : 2018-09-26 09:24:49
