START : 2018-08-13 11:31:20

            UPDATE TM_HO_COMPANY
            SET 
                BA_CODE = '1121',
                BA_NAME = 'TAP',
                CORE = 'SITE',
                COMPANY_CODE = '11',
                COMPANY_NAME = 'TRIPUTRA AGRO PERSADA',
                UPDATE_USER = 'NICHOLAS.BUDIHARDJA',
                UPDATE_TIME = SYSDATE
            WHERE ROWIDTOCHAR(ROWID) = 'AAAr3hAAeAAAxSbAAA';
        COMMIT;
END : 2018-08-13 11:31:20
