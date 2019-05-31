     /*
                      delete from orderdetail where orderkey like '%V%';
                      delete from orders where orderkey like '%V%';
                      update wave set batched = '0' ;
                      
                      uPDATE PICKDETAIL SET LOC = 'AA01' WHERE ID = 'RAMYK01';
UPDATE LOTXLOCXID SET LOC = 'AA01' WHERE ID = 'RAMYK01';
UPDATE SKUXLOC SET QTY = QTY -11, QTYALLOCATED = QTYALLOCATED - 11 WHERE SKU = 'ITEM001' AND LOC = 'PICKTO';
UPDATE SKUXLOC SET QTY = QTY +11, QTYALLOCATED = QTYALLOCATED + 11 WHERE SKU = 'ITEM001' AND LOC = 'AA01';
DELETE FROM TASKDETAIL WHERE STATUS < '5'

UPDATE TASKDETAIL SET TOLOC = 'PICKTO' WHERE STATUS < '5'
                      */