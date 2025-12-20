<?php

namespace humhub\modules\crm\models\traits;

use app\modules\crm\models\EntityLink;

trait LinkableTrait
{
    /**
     * @var array temporary storage for new links in the form
     */
    public $newLinks = [];

    /**
     * @var array temporary storage for edited links [id => url]
     */
    public $editLinks = [];

    /**
     * relation to saved Links
     */
    public function getExternalLinks()
    {
        return $this->hasMany(EntityLink::class, ['object_id' => 'id'])
            ->andWhere(['object_model' => self::class]);
    }

    /**
     * saves links and has to be called in afterSave() !
     */
    public function saveLinks()
    {
        // update und/or delete existing links
        $existingLinks = $this->externalLinks;

        // get IDs that are left in the form
        $keptIds = is_array($this->editLinks) ? array_keys($this->editLinks) : [];

        foreach ($existingLinks as $link) {
            if (in_array($link->id, $keptIds)) {
                // if link still exists -> update URL if changed
                if (isset($this->editLinks[$link->id]) && $link->url !== $this->editLinks[$link->id]) {
                    $link->url = $this->editLinks[$link->id];
                    $link->save();
                }
            } else {
                // Link ID is no more part of the form, so delete the object
                $link->delete();
            }
        }

        // set new links
        if (is_array($this->newLinks)) {
            foreach ($this->newLinks as $url) {
                if (!empty(trim($url))) {
                    $newLink = new EntityLink();
                    $newLink->object_model = self::class;
                    $newLink->object_id = $this->id;
                    $newLink->url = trim($url);
                    $newLink->save();
                }
            }
        }
    }
}
