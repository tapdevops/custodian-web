START : 2018-06-07 16:27:38

            DELETE FROM TR_RKT_VRA_SUM
            WHERE PERIOD_BUDGET = TO_DATE('01-01-2018','DD-MM-RRRR') 
                AND BA_CODE = '2121'
                AND VRA_CODE = 'DT010';
        
            INSERT INTO TR_RKT_VRA_SUM (PERIOD_BUDGET, BA_CODE, VRA_CODE, VALUE, INSERT_USER, INSERT_TIME)
            VALUES (
                TO_DATE('01-01-2018','DD-MM-RRRR'),
                '2121',
                'DT010',
                '6030.0787423123',
                'NICHOLAS.BUDIHARDJA',
                SYSDATE
            );
        COMMIT;
END : 2018-06-07 16:27:38
