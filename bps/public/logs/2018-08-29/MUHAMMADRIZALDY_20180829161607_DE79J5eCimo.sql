START : 2018-08-29 16:16:07

            UPDATE TM_HO_COST_CENTER
            SET DELETE_USER = 'MUHAMMAD.RIZALDY',
                DELETE_TIME = SYSDATE
            WHERE ROWIDTOCHAR(ROWID) = 'AAAr3bAAeAAAxSLAAL';
        COMMIT;
END : 2018-08-29 16:16:07