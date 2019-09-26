START : 2018-06-07 16:22:05

            DELETE FROM TR_RKT_VRA
            WHERE PERIOD_BUDGET = TO_DATE('01-01-2018','DD-MM-RRRR') 
                AND BA_CODE = '2121'
                AND NVL(VRA_CODE ,'--') = 'DT010' 
                AND NVL(DESCRIPTION_VRA,'--')  = 'Mobil Pemadam Kebakaran'
                AND NVL(INTERNAL_ORDER,'--')  = '-'
                AND TAHUN_ALAT  = '2018';
        
            DELETE FROM TR_RKT_VRA
            WHERE PERIOD_BUDGET = TO_DATE('01-01-2018','DD-MM-RRRR') 
                AND BA_CODE = '2121'
                AND NVL(VRA_CODE ,'--') = 'DT010' 
                AND NVL(DESCRIPTION_VRA,'--')  = 'Mobil Pemadam Kebakaran'
                AND NVL(INTERNAL_ORDER,'--')  = '-'
                AND TAHUN_ALAT = '2018';
        
            INSERT INTO TR_RKT_VRA (
                TRX_RKT_VRA_CODE, 
                PERIOD_BUDGET, 
                BA_CODE, 
                VRA_CODE, 
                DESCRIPTION_VRA, 
                JUMLAH_ALAT, 
                TAHUN_ALAT, 
                QTY_DAY, 
                DAY_YEAR_VRA, 
                JUMLAH_OPERATOR,
                JUMLAH_HELPER, 
                RVRA1_VALUE2, 
                RVRA17_VALUE1, 
                RVRA17_VALUE2, 
                RVRA12_VALUE2, 
                RVRA16_VALUE2, 
                RVRA15_VALUE1, 
                RVRA18_VALUE2, 
                FLAG_TEMP, 
                INSERT_USER, 
                INSERT_TIME,
                INTERNAL_ORDER,
                KOMPARISON_OUT_HM_KM,
                RP_QTY_BULAN_BUDGET
            )
            VALUES (
                '2018-2121-RKT021-DT010-EJ6yiF8Js49',
                TO_DATE('01-01-2018','DD-MM-RRRR'),        
                '2121',
                'DT010',
                'Mobil Pemadam Kebakaran',
                REPLACE('1',',',''), 
                REPLACE('2018',',',''), 
                REPLACE('',',',''), 
                REPLACE('',',',''), 
                REPLACE('',',',''), 
                REPLACE('',',',''), 
                REPLACE('',',',''), 
                REPLACE('',',',''), 
                REPLACE('',',',''), 
                REPLACE('',',',''), 
                REPLACE('',',',''), 
                REPLACE('',',',''), 
                REPLACE('',',',''),
                REPLACE('Y',',',''),
                'NICHOLAS.BUDIHARDJA',
                SYSDATE,
                '-',
                REPLACE('',',',''),
                REPLACE('',',','')
            );
        COMMIT;
END : 2018-06-07 16:22:06
