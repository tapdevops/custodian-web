START : 2018-09-25 21:04:25

                UPDATE TR_HO_SPD 
                SET 
                    PERIOD_BUDGET   = TO_DATE('2018', 'YYYY'),
                    DIV_CODE        = '',
                    CC_CODE         = '009',
                    RK_ID           = '228',
                    SPD_DESCRIPTION = 'Dinas',
                    COA_CODE        = '1205010101',
                    NORMA_SPD_ID    = '97',
                    CORE_CODE       = 'PLASMA',
                    COMP_CODE       = '64',
                    BA_CODE         = '6431',
                    PLAN            = '11',
                    GOLONGAN        = '4',
                    JLH_PRIA        = '1',
                    JLH_WANITA      = '',
                    JLH_HARI        = '2',
                    TIKET           = '3725469',
                    TRANSPORT_LAIN  = '500000',
                    UANG_MAKAN      = '210000',
                    UANG_SAKU       = '80000',
                    HOTEL_JLH_HARI  = '1',
                    HOTEL_JLH_TARIF = '450000',
                    OTHERS          = '0',
                    TOTAL           = '4965469',
                    REMARKS_OTHERS  = '',
                    SEBARAN_JAN     = '0',
                    SEBARAN_FEB     = '0',
                    SEBARAN_MAR     = '0',
                    SEBARAN_APR     = '0',
                    SEBARAN_MAY     = '0',
                    SEBARAN_JUN     = '0',
                    SEBARAN_JUL     = '0',
                    SEBARAN_AUG     = '0',
                    SEBARAN_SEP     = '0',
                    SEBARAN_OCT     = '0',
                    SEBARAN_NOV     = '4965469',
                    SEBARAN_DEC     = '0',
                    SEBARAN_TOTAL   = '4965469',
                    TIPE_NORMA      = 'UMUM',
                    UPDATE_USER     = 'MUHAMMAD.RIZALDY',
                    UPDATE_TIME     = SYSDATE
                WHERE ROWIDTOCHAR(ROWID) = 'AAAsHlAAaAACaleAAI';
            
                INSERT INTO TR_HO_SPD (
                    PERIOD_BUDGET,
                    DIV_CODE,
                    CC_CODE,
                    RK_ID,
                    SPD_DESCRIPTION,
                    COA_CODE,
                    NORMA_SPD_ID,
                    CORE_CODE,
                    COMP_CODE,
                    BA_CODE,
                    PLAN,
                    GOLONGAN,
                    JLH_PRIA,
                    JLH_WANITA,
                    JLH_HARI,
                    TIKET,
                    TRANSPORT_LAIN,
                    UANG_MAKAN,
                    UANG_SAKU,
                    HOTEL_JLH_HARI,
                    HOTEL_JLH_TARIF,
                    OTHERS,
                    TOTAL,
                    REMARKS_OTHERS,
                    SEBARAN_JAN,
                    SEBARAN_FEB,
                    SEBARAN_MAR,
                    SEBARAN_APR,
                    SEBARAN_MAY,
                    SEBARAN_JUN,
                    SEBARAN_JUL,
                    SEBARAN_AUG,
                    SEBARAN_SEP,
                    SEBARAN_OCT,
                    SEBARAN_NOV,
                    SEBARAN_DEC,
                    SEBARAN_TOTAL,
                    TIPE_NORMA,
                    INSERT_USER,
                    INSERT_TIME
                ) VALUES (
                    TO_DATE('2018', 'YYYY'),
                    '',
                    '009',
                    '236',
                    'Apa Saja',
                    '1210010101',
                    '66',
                    'HO',
                    '51',
                    '5111',
                    '1',
                    '6',
                    '1',
                    '',
                    '2',
                    '6489210',
                    '500000',
                    '0',
                    '0',
                    '1',
                    '750000',
                    '0',
                    '7739210',
                    '',
                    '7739210',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '7739210',
                    'UMUM',
                    'MUHAMMAD.RIZALDY',
                    SYSDATE
                );
            COMMIT;
END : 2018-09-25 21:04:25
