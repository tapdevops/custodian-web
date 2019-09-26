START : 2018-08-07 11:09:24

                UPDATE TR_HO_SPD 
                SET 
                    PERIOD_BUDGET   = TO_DATE('2018', 'YYYY'),
                    DIV_CODE        = 'D00054',
                    CC_CODE         = 'D00057',
                    RK_ID           = '64',
                    SPD_DESCRIPTION = 'Contoh Rencana Kerja IT Consultings',
                    COA_CODE        = '6201011202',
                    NORMA_SPD_ID    = '3',
                    CORE_CODE       = 'SITE',
                    COMP_CODE       = '11',
                    BA_CODE         = '1121',
                    PLAN            = '7',
                    GOLONGAN        = '1',
                    JLH_PRIA        = '2',
                    JLH_WANITA      = '1',
                    JLH_HARI        = '2',
                    TIKET           = '2400000',
                    TRANSPORT_LAIN  = '0',
                    UANG_MAKAN      = '198000',
                    UANG_SAKU       = '254100',
                    HOTEL_JLH_HARI  = '2',
                    HOTEL_JLH_TARIF = '1815000',
                    OTHERS          = '500000',
                    TOTAL           = '5167100',
                    REMARKS_OTHERS  = 'jalan-jalan',
                    SEBARAN_JAN     = '0',
                    SEBARAN_FEB     = '0',
                    SEBARAN_MAR     = '0',
                    SEBARAN_APR     = '0',
                    SEBARAN_MAY     = '0',
                    SEBARAN_JUN     = '0',
                    SEBARAN_JUL     = '5167100',
                    SEBARAN_AUG     = '0',
                    SEBARAN_SEP     = '0',
                    SEBARAN_OCT     = '0',
                    SEBARAN_NOV     = '0',
                    SEBARAN_DEC     = '0',
                    SEBARAN_TOTAL   = '5167100',
                    UPDATE_USER     = 'NICHOLAS.BUDIHARDJA',
                    UPDATE_TIME     = SYSDATE
                WHERE ROWIDTOCHAR(ROWID) = 'AAAsHlAAaAACaleAAB';
            COMMIT;
END : 2018-08-07 11:09:25