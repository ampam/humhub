<?php

namespace humhub\modules\activity\stream;

use humhub\modules\stream\models\ContentContainerStreamQuery;
use humhub\modules\user\models\User;

/**
 * This stream query can be used for streams which support default content as well as activity streams.
 *
 * The behavior of this query can be switching by changing the `activity` flag.
 *
 * @package humhub\modules\activity\stream
 * @since 1.8
 */
class ActivityStreamQuery extends ContentContainerStreamQuery
{
    /**
     * @var bool activates or deactivates activity stream behavior
     */
    public $activity = true;

    /**
     * @inheritDoc
     */
    public $pinnedContentSupport = false;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        if($this->activity) {
            $this->preventSuppression = true;
        }
    }

    /**
     * @inheritDoc
     */
    public function afterApplyFilters()
    {
        if($this->activity) {
            $this->channel(self::CHANNEL_ACTIVITY);

            $this->query()->andWhere(['!=', 'user.status', User::STATUS_NEED_APPROVAL]);

            // Exclude own activities
            if ($this->user) {
                $this->query()->andWhere('content.created_by != :userId', [':userId' => $this->user->id]);
            }
        }
        parent::afterApplyFilters();
    }
}
