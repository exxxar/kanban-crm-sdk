<?php

namespace Exxxar\Kanban\Services;

use GuzzleHttp\Client;

class Tasks
{
    public function __construct(protected Client $http) {}

    public function create(array $data)
    {
        return $this->http->post('api/task/create', ['json' => $data])->getBody()->getContents();
    }

    public function comments($taskId)
    {
        return $this->http->get("api/task/$taskId/comments")->getBody()->getContents();
    }

    public function addComment($taskId, array $data)
    {
        return $this->http->post("api/task/$taskId/comment", ['multipart' => $data])->getBody()->getContents();
    }

    public function attachments($taskId)
    {
        return $this->http->get("api/task/$taskId/attachments")->getBody()->getContents();
    }

    public function uploadAttachments($taskId, array $files)
    {
        return $this->http->post("api/task/$taskId/attachments", ['multipart' => $files])->getBody()->getContents();
    }
}
