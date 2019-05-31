Steps for archive process:

Note: Please restore a copy of your production database into a new location and attempt the steps below prior to moving data in a "LIVE" system.
      User can add sleep time (e.g. 1 sec.) between batch for reducing database traffic by removing comment in the code "WAITFOR DELAY '00:00:01' " of each script.	

1) Read the notes and replace !PROD_DB!.!PROD_WH! and !ARC_DB!.!ARC_WH! accordingly in each script in SP folder.
2) Check if the following tables exist in the archive db. If it does not exist please create. 
	--PO (PODetail, POSTATUSHISTORY, PODETAILSTATUSHISTORY), 
	--Receipt (ReceiptDetail, LPNDetail, RECEIPTSTATUSHISTORY, RECEIPTDETAILSTATUSHISTORY), 
	--Orders (OrderDetail, PreAllocatePickDetail, xPickDetail, PickDetail), 
	--Adjustment (AdjustmentDetail), Transfer (TransferDetail), 
	--Wave (WaveDetail), DropID (DropIDDetail), 
 	--TaskDetail, Itrn (itrnserial), ACCUMULATEDCHARGES,
 	--TransASN (TransASND), TransShip (TransDetail), XOrders (XOrderDetail)
	--WorkOrder (RouteOPS, OPXShipORD, OPSSkuDet), 
	--LoadHDR (LoadStop, LoadStopSeal, LoadUnitDetail, LoadOrderDetail)
	--DROPID (DropID,  DropIDDetail)
	--PACKOUT (PackOut,  PackOutDetail)
        --Wave plan tables: WP_OrderHeader (WP_Orderline), WP_Wave (WP_WaveDetails), WP_EVENTLOG

3) Compare table's columns between archiv database and production database and ensure tables they are mached.
4) Run each script in SP folder to create the stored procedures in the archive database. 
5) Check if alert table exists in the archive db. 
  If it does not exist please run the following sQL statement:
CREATE TABLE ALERT
(
	SERIALKEY	INT		IDENTITY(1,1)		NOT NULL,
	WHSEID		VARCHAR(30)	DEFAULT USER_NAME(),
	ALERTKEY	VARCHAR(18)				NOT NULL,		
	MODULENAME	VARCHAR(30)				NOT NULL,		
	ALERTMESSAGE	VARCHAR(255)				NOT NULL,		
	SEVERITY	INT		DEFAULT	5		NOT NULL,		
	LOGDATE		DATETIME	DEFAULT	GETDATE()	NOT NULL,		
	USERID		VARCHAR(18)	DEFAULT	USER_NAME()	NOT NULL,		
	NOTIFYID	VARCHAR(18)	DEFAULT	''		NOT NULL,		
	STATUS		VARCHAR(10)	DEFAULT	'0'		NOT NULL,		
	ADDDATE		DATETIME	DEFAULT	GETDATE()	NOT NULL,		
	ADDWHO		VARCHAR(18)	DEFAULT	USER_NAME()	NOT NULL,		
	EDITDATE	DATETIME	DEFAULT	GETDATE()	NOT NULL,		
	EDITWHO		VARCHAR(18)	DEFAULT	USER_NAME()	NOT NULL,		
	RESOLUTION	TEXT		DEFAULT	''		NOT NULL
);
  
6) Run a stored procedure created in step 4) by "EXECUTE stored_procedure_name @Arc_days = 90". 
  The @Arc_days is days you want to keep in Source database (default to 90).
  e.g. EXECUTE archive_Adjustment @Arc_days = 90"
	
7) Check archive table after the stored procedure has been executed. 
	The archivedb alert table will contain information about error or total rows processed.

8) Repeat step 6) and 7) for each stored procedure. 

