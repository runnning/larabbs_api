<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Topic
 *
 * @property int $id
 * @property string $title
 * @property string $body
 * @property int $user_id
 * @property int $category_id
 * @property int $reply_count
 * @property int $view_count
 * @property int $last_reply_user_id
 * @property int $order
 * @property string|null $excerpt
 * @property string|null $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Category $category
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Reply[] $replies
 * @property-read int|null $replies_count
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\TopicFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Topic newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Model ordered()
 * @method static \Illuminate\Database\Eloquent\Builder|Topic query()
 * @method static \Illuminate\Database\Eloquent\Builder|Topic recent()
 * @method static \Illuminate\Database\Eloquent\Builder|Topic recentReplied()
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereExcerpt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereLastReplyUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereReplyCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereViewCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic withOrder($order)
 * @mixin \Eloquent
 */
class Topic extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'body', 'excerpt', 'slug','category_id'];

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function replies(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
      return $this->hasMany(Reply::class);
    }

    /**
     * 局部作用域
    */
    public function scopeWithOrder(Builder $query,?string $order): void
    {
        // 不同的排序，使用不同的数据读取逻辑
//        switch ($order) {
//            case 'recent':
//                $query->recent();
//                break;
//
//            default:
//                $query->recentReplied();
//                break;
//        }
        if($order==='recent'){
          $query->recent();
        }else{
          $query->recentReplied();
        }
    }

    public function scopeRecentReplied(Builder $query): Builder
    {
        // 当话题有新回复时，更新话题模型的 reply_count 属性，
        // 此时会自动触发框架对数据模型 updated_at 时间戳的更新
        return $query->orderBy('updated_at', 'desc');
    }

    public function scopeRecent($query):Builder
    {
        // 按照创建时间排序
        return $query->orderBy('created_at', 'desc');
    }

    //生成链接
    public function link($params=[]): string
    {
        return route('topics.show',array_merge([$this->id,$this->slug],$params));
    }

    public function updateReplyCount(): void
    {
        $this->reply_count = $this->replies->count();
        $this->save();
    }

    public function topReplies(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
      return $this->replies()->limit(5);
    }
}
