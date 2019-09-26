START : 2018-08-23 15:28:45

      MERGE INTO TR_RKT_VRA_DISTRIBUSI VD 
      USING (
        SELECT
        R.PERIOD_BUDGET, HS.BA_CODE, R.ACTIVITY_CODE, I.SUB_COST_ELEMENT VRA_CODE, HS.AFD_CODE,
        SUM(R.TOTAL_RP_SETAHUN/VS.VALUE) HM_KM,
        MAX(VS.VALUE) PRICE_QTY_VRA, 
        SUM(R.TOTAL_RP_SETAHUN) PRICE_HM_KM, 
        EXTRACT(YEAR FROM R.PERIOD_BUDGET)||'-'||HS.BA_CODE||'-RKT015-'||R.ACTIVITY_CODE||'-'||I.SUB_COST_ELEMENT TRX_CODE,
        'INFRA' TIPE_TRANSAKSI
        FROM TR_RKT R
        JOIN TM_HECTARE_STATEMENT HS ON HS.BA_CODE = R.BA_CODE AND HS.PERIOD_BUDGET = R.PERIOD_BUDGET
          AND HS.AFD_CODE = R.AFD_CODE AND HS.BLOCK_CODE = R.BLOCK_CODE
        JOIN TN_INFRASTRUKTUR I ON I.ACTIVITY_CODE = R.ACTIVITY_CODE AND I.PERIOD_BUDGET = R.PERIOD_BUDGET
          AND I.BA_CODE = R.BA_CODE AND I.LAND_TYPE = HS.LAND_TYPE AND I.TOPOGRAPHY = HS.TOPOGRAPHY
        JOIN TR_RKT_VRA_SUM VS ON VS.BA_CODE = R.BA_CODE AND VS.PERIOD_BUDGET = R.PERIOD_BUDGET
          AND VS.VRA_CODE = I.SUB_COST_ELEMENT
        WHERE R.BA_CODE = '2121' AND R.PLAN_SETAHUN > 0
        AND R.ACTIVITY_CODE = '20330' 
        AND EXTRACT(YEAR FROM R.PERIOD_BUDGET) = '2018'
        AND R.SUMBER_BIAYA = 'INTERNAL'
        GROUP BY R.PERIOD_BUDGET, HS.BA_CODE, R.ACTIVITY_CODE, I.SUB_COST_ELEMENT, HS.AFD_CODE
      ) RKT
      ON (
        RKT.PERIOD_BUDGET = VD.PERIOD_BUDGET AND RKT.BA_CODE = VD.BA_CODE AND RKT.ACTIVITY_CODE = VD.ACTIVITY_CODE
        AND RKT.VRA_CODE = VD.VRA_CODE AND RKT.AFD_CODE = VD.LOCATION_CODE
      )
      WHEN MATCHED THEN UPDATE SET
        VD.PERIOD_BUDGET = RKT.PERIOD_BUDGET,
        VD.BA_CODE = RKT.BA_CODE,
        VD.ACTIVITY_CODE = RKT.ACTIVITY_CODE,
        VD.VRA_CODE = RKT.VRA_CODE,
        VD.LOCATION_CODE = RKT.AFD_CODE,
        VD.HM_KM = RKT.HM_KM,
        VD.PRICE_QTY_VRA = RKT.PRICE_QTY_VRA,
        VD.PRICE_HM_KM = RKT.PRICE_HM_KM,
        VD.TRX_CODE = RKT.TRX_CODE,
        VD.TIPE_TRANSAKSI = RKT.TIPE_TRANSAKSI,
        VD.UPDATE_USER = 'DARMA.PRIHARTONI',
        VD.UPDATE_TIME = CURRENT_TIMESTAMP
      WHEN NOT MATCHED THEN INSERT (
        PERIOD_BUDGET,BA_CODE,ACTIVITY_CODE,VRA_CODE,LOCATION_CODE,HM_KM,PRICE_QTY_VRA,
        PRICE_HM_KM,TRX_CODE,TIPE_TRANSAKSI,INSERT_USER,INSERT_TIME
      )
      VALUES (
        RKT.PERIOD_BUDGET,RKT.BA_CODE,RKT.ACTIVITY_CODE,RKT.VRA_CODE,RKT.AFD_CODE,RKT.HM_KM,RKT.PRICE_QTY_VRA,
        RKT.PRICE_HM_KM,RKT.TRX_CODE,RKT.TIPE_TRANSAKSI,'DARMA.PRIHARTONI',CURRENT_TIMESTAMP
      )
    COMMIT;
END : 2018-08-23 15:28:45
