<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\CastMember
 *
 * @property string $id
 * @property string $name
 * @property int $type
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|CastMember newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CastMember newQuery()
 * @method static \Illuminate\Database\Query\Builder|CastMember onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CastMember query()
 * @method static \Illuminate\Database\Eloquent\Builder|CastMember whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CastMember whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CastMember whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CastMember whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CastMember whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CastMember whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CastMember whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|CastMember withTrashed()
 * @method static \Illuminate\Database\Query\Builder|CastMember withoutTrashed()
 */
	class CastMember extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Category
 *
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category newQuery()
 * @method static \Illuminate\Database\Query\Builder|Category onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Category withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Category withoutTrashed()
 */
	class Category extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Genre
 *
 * @property string $id
 * @property string $name
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Category[] $categories
 * @property-read int|null $categories_count
 * @method static \Illuminate\Database\Eloquent\Builder|Genre newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Genre newQuery()
 * @method static \Illuminate\Database\Query\Builder|Genre onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Genre query()
 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Genre withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Genre withoutTrashed()
 */
	class Genre extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Video
 *
 * @property string $id
 * @property string $title
 * @property string $description
 * @property int $year_launched
 * @property bool $opened
 * @property string $rating
 * @property int $duration
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Category[] $categories
 * @property-read int|null $categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Genre[] $genres
 * @property-read int|null $genres_count
 * @method static \Illuminate\Database\Eloquent\Builder|Video newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Video newQuery()
 * @method static \Illuminate\Database\Query\Builder|Video onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Video query()
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereOpened($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Video whereYearLaunched($value)
 * @method static \Illuminate\Database\Query\Builder|Video withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Video withoutTrashed()
 */
	class Video extends \Eloquent {}
}

namespace App{
/**
 * App\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

