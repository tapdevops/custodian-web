
    #!/bin/bash
    host='10.20.1.111';
    user='BPS_PROD';
    pass='bps_prod';
    dbname='tapapps';
    
    export ORACLE_HOME=/home/oracle/app/oracle/product/12.1.0/dbhome_1
    export ORACLE_SID=$dbname
    export LD_LIBRARY_PATH=$ORACLE_HOME/lib:/usr/lib
    export PATH=$PATH:$ORACLE_HOME/bin
    
    /home/oracle/app/oracle/product/12.1.0/dbhome_1/bin/sqlplus -s $user/$pass@$host:1521/$dbname < /var/www/html/bps/public/tmp_query/MUHAMMADRIZALDY_20180910201524_HMIq1h7jbI6_00_HO_ACTOUT_01_SAVETEMP.sql