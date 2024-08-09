<?php
namespace Interfaces;

interface HabitatServiceInterface {
    public function getAllHabitats();
    public function getHabitatById($habitatId);
    public function updateHabitat($id, $name, $description, $image);
    public function uploadImage($file);
    public function addHabitat($name, $description, $image);
    public function deleteHabitat($id);
    public function getAnimalsByHabitat($id);
    public function getApprovedComments($id);
    public function getAllHabitatComments();
    public function deleteHabitatComment($comment_id);
    public function approveHabitatComment($comment_id);
    public function submitHabitatComment($habitatId, $vetId, $comment);
}
