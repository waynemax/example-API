<?php
    function setRequisites($params, $user_id) : int {
        $params['real_author_id'] = $user_id;
        $params['create_date'] = time();
        $insert = qInsert('requisites', $params, false);
        squery($insert['query']);
        return insertID();
    }

    function checkRequisite($id) {
        $select = select(['real_author_id', 'organization', 'status', 'balance'], ['requisites'], "id='{$id}'");
        $query = sfetch($select);
        return ($query ?? false);
    }

    function updateRequisites($id, $params) {
        $update = qUpdate('requisites', $params, "id='{$id}'");
        squery($update);
        return true;
    }

    function updateStatus(int $id, bool $status) : bool {
        $params = [
            'status' => $status ? 1 : 0
        ];
        $update = qUpdate('requisites', $params, "id='{$id}'");
        squery($update);
        return true;
    }

    function getCountRequisites(int $user_id) : array {
        $select = select(["status"], ['requisites'], "real_author_id='{$user_id}'");
        $query = sfetch($select, true);
        $active = 0;
        foreach ($query as $value) {
            if ($value['status'] == 1) {
                $active++;
            }
        }
        $all = count($query);
       return [
            'all' => $all,
            'active' => $active,
        ];
    }

    function removeRequisite(int $id) : bool{
        $query = "DELETE FROM `requisites` WHERE id='{$id}'";
        squery($query);
        return true;
    }

    function shareBalance($balance, $user_id) {
        $update = "UPDATE `requisites` SET balance = balance + {$balance} WHERE real_author_id='{$user_id}' AND status = 1";
        squery($update);
        return true;
    }

    function requisitesGet($user_id = NULL, $offset = 0, $count = 20){
        $select = select(['*'], ['requisites'], "real_author_id='{$user_id}'", ['INN DESC'], $offset, $count);
        $query = sfetch($select, true);
        $query = array_filter($query, function($key) {
            return array_filter($key, function ($value) {
                return !empty($value);
            });
        });
        cout($query);
    }