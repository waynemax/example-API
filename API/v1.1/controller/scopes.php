<?php
    function getScopesById(int $user_id) : array {
        $query = select(['scopes_id'], ['users_scopes'], "user_id='{$user_id}'", false);
        $scopes = sfetch($query, true);
        $scopesList = array_map(function ($v){return num($v['scopes_id']);} ,$scopes);
        return array_merge($scopesList, [SCOPE_USER_ID]);
    }

    function isAllowChangeScopes(array $user_scopes, array $for_scopes) : bool{
        $config = config();
        $allow = array(
            SCOPE_SUPERVISOR_ID => [SCOPE_USER_ID, SCOPE_FINANCE_ID, SCOPE_MODERATOR_ID, SCOPE_SHOP_ID],
            SCOPE_ADMIN_ID => [SCOPE_USER_ID, SCOPE_FINANCE_ID, SCOPE_MODERATOR_ID, SCOPE_SHOP_ID],
            SCOPE_FINANCE_ID => [SCOPE_USER_ID, SCOPE_MODERATOR_ID, SCOPE_SHOP_ID],
            SCOPE_MODERATOR_ID => [SCOPE_USER_ID]
        );
        $allow[$config['SCOPE_ADMIN_KEY']] = array_merge($allow[SCOPE_ADMIN_ID], [SCOPE_ADMIN_ID, SCOPE_SUPERVISOR_ID]);
        $main_user_scope = max($user_scopes);
        $main_for_scope = max($for_scopes);
        return ($allow[$main_user_scope] && in_array($main_for_scope, $allow[$main_user_scope]));
    }

    function setScopes(int $user_id, int $for_id, int $scopes_id): array {
        $insertFields = [
            "user_id" => $for_id,
            "admin_id" => $user_id,
            "scopes_id" => $scopes_id,
            "create_date" => time()
        ];
        $insert = qInsert('users_scopes', $insertFields, false);
        squery($insert['query']);
        $id = insertID();
        return [
            'status' => true,
            'id' => $id
        ];
    }

    function removeScopes(int $for_id, int $scopes_id): array {
        squery("DELETE FROM `users_scopes` WHERE user_id='{$for_id}' AND scopes_id='{$scopes_id}'");
        return [
            'status' => true
        ];
    }