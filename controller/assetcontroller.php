<?php
require_once 'controller.php';

class Asset extends Controller {
    function retrieveAssets() {
        $this->setStatement("SELECT A.*, C.category_name, SC.sub_category_name, T.type_name, CO.asset_condition_name, S.status_name 
            FROM itam_asset A
            JOIN itam_asset_category C ON A.category_id = C.category_id
            JOIN itam_asset_sub_category SC ON A.sub_category_id = SC.sub_category_id
            JOIN itam_asset_type T ON A.type_id = T.type_id
            JOIN itam_asset_condition CO ON A.asset_condition_id = CO.asset_condition_id
            JOIN itam_asset_status S ON A.availability_status = S.status_id");

        $this->statement->execute();
        $this->sendJsonResponse($this->statement->fetchAll());
    }

    function retrieveOneAsset($id) {
        $this->setStatement("SELECT * FROM itam_asset WHERE asset_id = ?");
        $this->statement->execute([$id]);
        $result = $this->statement->fetch();
        $this->sendJsonResponse($result ?: ["error" => "Asset not found"], $result ? 200 : 404);
    }

    function insertAsset($data) {
        extract($data);
        $this->setStatement("INSERT INTO itam_asset (asset_name, serial_number, category_id, sub_category_id, type_id, asset_condition_id, availability_status, location, specifications, warranty_duration, aging, warranty_due_date, purchase_date, notes) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $success = $this->statement->execute([$asset_name, $serial_number, $category_id, $sub_category_id, $type_id, $asset_condition_id, $availability_status, $location, $specifications, $warranty_duration, $aging, $warranty_due_date, $purchase_date, $notes]);

        $this->sendJsonResponse(["message" => $success ? "Asset added successfully" : "Failed to add asset"], $success ? 201 : 500);
    }

    function updateAsset($id, $data) {
        extract($data);
        $this->setStatement("UPDATE itam_asset 
            SET asset_name = ?, serial_number = ?, category_id = ?, sub_category_id = ?, type_id = ?, asset_condition_id = ?, availability_status = ?, location = ?, specifications = ?, warranty_duration = ?, aging = ?, warranty_due_date = ?, purchase_date = ?, notes = ? 
            WHERE asset_id = ?");

        $success = $this->statement->execute([$asset_name, $serial_number, $category_id, $sub_category_id, $type_id, $asset_condition_id, $availability_status, $location, $specifications, $warranty_duration, $aging, $warranty_due_date, $purchase_date, $notes, $id]);

        $this->sendJsonResponse(["message" => $success ? "Asset updated successfully" : "Failed to update asset"], $success ? 200 : 500);
    }

    function deleteAsset($id) {
        $this->setStatement("DELETE FROM itam_asset WHERE asset_id = ?");
        $success = $this->statement->execute([$id]);
        $this->sendJsonResponse(["message" => $success ? "Asset deleted successfully" : "Failed to delete asset"], $success ? 200 : 500);
    }

    /**
     * Get predefined repair urgency levels
     */
    function getRepairUrgencyLevels() {
        $this->setStatement("SELECT * FROM `itam_repair_urgency");
        $this->statement->execute([]);
        $result = $this->statement->fetchAll();
        $this->sendJsonResponse($result ?: ["error" => "Repair Urgency not found"], $result ? 200 : 404);
    }

    /**
     * Get assets with any repair urgency level (Critical, High, Medium, Low)
     */
    function getRepairUrgencyAssets() {
        $this->setStatement("SELECT A.asset_id, A.asset_name, C.category_name, SC.sub_category_name, 
                                    R.issue, R.remarks, R.urgency_id, U.urgency_level 
                             FROM itam_asset A
                             JOIN itam_asset_category C ON A.category_id = C.category_id
                             JOIN itam_asset_sub_category SC ON A.sub_category_id = SC.sub_category_id
                             JOIN itam_asset_repair_request R ON A.asset_id = R.asset_id
                             JOIN itam_repair_urgency U ON R.urgency_id = U.urgency_id
                             WHERE R.urgency_id IN (1, 2, 3, 4)  -- 1 = Critical, 2 = High, 3 = Medium, 4 = Low
                             ORDER BY R.urgency_id ASC");

        $this->statement->execute();
        $result = $this->statement->fetchAll();

        $this->sendJsonResponse($result ?: ["error" => "No assets with repair urgency found"], $result ? 200 : 404);
    }
       /**
     * Retrieve all asset conditions.
     */
    function getAssetCondition() {
        $this->setStatement("SELECT * FROM itam_asset_condition ORDER BY asset_condition_id ASC");
        $this->statement->execute();
        $result = $this->statement->fetchAll();
        $this->sendJsonResponse($result ?: ["error" => "No asset conditions found"], $result ? 200 : 404);
    }

    /**
     * Retrieve all asset statuses.
     */
    function getAssetStatus() {
        $this->setStatement("SELECT * FROM itam_asset_status ORDER BY status_id ASC");
        $this->statement->execute();
        $result = $this->statement->fetchAll();
        $this->sendJsonResponse($result ?: ["error" => "No asset statuses found"], $result ? 200 : 404);
    }
}
?>
