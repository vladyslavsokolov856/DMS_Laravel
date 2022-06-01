<?php

declare(strict_types=1);

namespace App\Infrastructures\Models\Eloquent;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class MailCategory extends BaseModel
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'annotation',
        'is_specify_number_days',
        'sort',
    ];

    protected const DOMAIN_EXPIRATION_NAME = 'domain_expiration';

    /**
     * @param string $mailCategoryName
     * @return boolean
     */
    public function isMatchByMailCategoryName(string $mailCategoryName): bool
    {
        return $this->name == $mailCategoryName;
    }

    /**
     * @return boolean
     */
    public function isDomainExpiration(): bool
    {
        return $this->isMatchByMailCategoryName(self::DOMAIN_EXPIRATION_NAME);
    }
}
