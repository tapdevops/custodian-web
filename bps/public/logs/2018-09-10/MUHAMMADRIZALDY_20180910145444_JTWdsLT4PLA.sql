START : 2018-09-10 14:54:44

            UPDATE TM_HO_COST_CENTER
            SET DELETE_USER = 'MUHAMMAD.RIZALDY',
                DELETE_TIME = SYSDATE
            WHERE ROWIDTOCHAR(ROWID) = 'AAAr3bAAeAAAxSLAAJ';
        COMMIT;
END : 2018-09-10 14:54:44