START : 2018-09-25 20:44:04

                UPDATE TR_HO_SPD 
                SET 
                    PERIOD_BUDGET   = TO_DATE('2018', 'YYYY'),
                    DIV_CODE        = '',
                    CC_CODE         = '009',
                    RK_ID           = '229',
                    SPD_DESCRIPTION = 'Pelatihan Sistem Mobile Estate',
                    COA_CODE        = '6201010401',
                    NORMA_SPD_ID    = '98',
                    CORE_CODE       = 'HO',
                    COMP_CODE       = '41',
                    BA_CODE         = '4111',
                    PLAN            = '10',
                    GOLONGAN        = '4',
                    JLH_PRIA        = '1',
                    JLH_WANITA      = '',
                    JLH_HARI        = '2',
                    TIKET           = '2442388',
                    TRANSPORT_LAIN  = '2500000',
                    UANG_MAKAN      = '462000',
                    UANG_SAKU       = '193600',
                    HOTEL_JLH_HARI  = '0',
                    HOTEL_JLH_TARIF = '0',
                    OTHERS          = '0',
                    TOTAL           = '5597988',
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
                    SEBARAN_OCT     = '5597988',
                    SEBARAN_NOV     = '0',
                    SEBARAN_DEC     = '0',
                    SEBARAN_TOTAL   = '5597988',
                    TIPE_NORMA      = 'UMUM',
                    UPDATE_USER     = 'MUHAMMAD.RIZALDY',
                    UPDATE_TIME     = SYSDATE
                WHERE ROWIDTOCHAR(ROWID) = 'AAAsHlAAaAACalfAAC';
            
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
                    '229',
                    'Training',
                    '1205010101',
                    '79',
                    'PLASMA',
                    '52',
                    '5231',
                    '8',
                    '4',
                    '1',
                    '',
                    '2',
                    '0',
                    '2900000',
                    '210000',
                    '80000',
                    '0',
                    '0',
                    '0',
                    '3190000',
                    '',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '3190000',
                    '0',
                    '0',
                    '0',
                    '0',
                    '3190000',
                    'UMUM',
                    'MUHAMMAD.RIZALDY',
                    SYSDATE
                );
            COMMIT;
END : 2018-09-25 20:44:05