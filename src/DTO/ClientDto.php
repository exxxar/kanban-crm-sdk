<?php

namespace Exxxar\Kanban\DTO;

use Carbon\Carbon;

class ClientDto
{
    public function __construct(
        public int $id,
        public int $task_id,
        public ?string $company_name,
        public ?string $contact_person,
        public ?string $phone,
        public ?string $source,
        public ?string $address,
        public ?string $placement_type,
        public ?float $cost,
        public ?string $partner,
        public ?string $deal_comment,
        public array $links,
        public array $custom_data,
        public ?Carbon $created_at,
        public ?Carbon $updated_at,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? 0,
            task_id: $data['task_id'] ?? 0,
            company_name: $data['company_name'] ?? null,
            contact_person: $data['contact_person'] ?? null,
            phone: $data['phone'] ?? null,
            source: $data['source'] ?? null,
            address: $data['address'] ?? null,
            placement_type: $data['placement_type'] ?? null,
            cost: isset($data['cost']) ? (float) $data['cost'] : null,
            partner: $data['partner'] ?? null,
            deal_comment: $data['deal_comment'] ?? null,
            links: $data['links'] ?? [],
            custom_data: $data['custom_data'] ?? [],
            created_at: isset($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            updated_at: isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
        );
    }

    public function getFullName(): string
    {
        return $this->company_name ?: $this->contact_person ?: 'Без имени';
    }

    public function hasCost(): bool
    {
        return $this->cost !== null && $this->cost > 0;
    }
}