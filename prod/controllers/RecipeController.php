<?php
class RecipeController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Fetch recipe details by ID
    public function show($id) {
        $stmt = $this->db->prepare("SELECT * FROM recipes WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
