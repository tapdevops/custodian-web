START : 2018-08-01 16:40:56

            UPDATE TM_HO_COST_CENTER
            SET 
                HCC_CC = 'D00004',
                HCC_DIVISI = 'D00004',
                HCC_COST_CENTER = 'COMMISSIONER',
                HCC_COST_CENTER_HEAD = 'COMM HEADs',
                HCC_DIVISION_HEAD = 'abc',
                UPDATE_USER = 'NICHOLAS.BUDIHARDJA',
                UPDATE_TIME = SYSDATE
            WHERE ROWIDTOCHAR(ROWID) = 'AAAr3bAAeAAAxSOAAA';
        COMMIT;
END : 2018-08-01 16:40:58
