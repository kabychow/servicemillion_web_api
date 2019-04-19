<?php

$app->db->add('auth', function (mysqli_stmt $stmt, $api_key)
{
    $stmt->prepare("
      SELECT id
      FROM user_auth_view
      WHERE api_key = ?
    ");
    $stmt->bind_param('s', $api_key);
    $stmt->execute();
    return $stmt->get_result()->fetch_row()[0] ?? null;
});


$app->db->add('login', function (mysqli_stmt $stmt, $email, $password)
{
    $stmt->prepare("
      SELECT id, password
      FROM user
      WHERE email = ?
    ");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    if ($user = $stmt->get_result()->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $stmt->prepare("
              UPDATE user
              SET last_login_at = CURRENT_TIMESTAMP
              WHERE id = ?
            ");
            $stmt->bind_param('i', $user['id']);
            $stmt->execute();
            $stmt->prepare("
              SELECT api_key
              FROM user_auth_view
              WHERE id = ?
            ");
            $stmt->bind_param('i', $user['id']);
            $stmt->execute();
            return $stmt->get_result()->fetch_row()[0];
        }
    }
    return null;
});


$app->db->add('get_responses', function (mysqli_stmt $stmt, $user_id) use ($app)
{
    $responses = [];
    $stmt->prepare("
      SELECT id, data
      FROM response
      WHERE client_id = (
        SELECT client_id
        FROM user
        WHERE id = ?)
        AND is_deleted = 0
      ORDER BY id DESC
    ");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($response = $result->fetch_assoc()) {
        $response['data'] = json_decode($response['data'], true);
        $responses[] = $response;
    }
    return $responses;
});
