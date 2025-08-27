<?php

/**
 * Elgg file uploader/edit action
 */

namespace wZm\Files\DiskQuota\Actions;

class Upload
{
    public function __invoke(\Elgg\Request $request)
    {
        $title = htmlspecialchars((string) $request->getParam('title'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $desc = (string) $request->getParam('description');
        $access_id = (int) $request->getParam('access_id');
        $container_guid = (int) $request->getParam('container_guid', 0);
        $guid = (int) $request->getParam('file_guid');
        $tags = (string) $request->getParam('tags');

        $container_guid = $container_guid ?: elgg_get_logged_in_user_guid();
		
        // check if upload attempted and failed
        $uploaded_file = elgg_get_uploaded_file('upload', false);

        // check disk quota
        if ($uploaded_file) {
            $disk_quota = new \wZm\Files\DiskQuota\Service\Quota(elgg_get_logged_in_user_entity());

            if (!(bool) $disk_quota->validate()) {
                return elgg_error_response(elgg_echo('diskquota:limit'));
            }
        }

        if ($uploaded_file && !$uploaded_file->isValid()) {
            $error = elgg_get_friendly_upload_error($uploaded_file->getError());
            return elgg_error_response($error);
        }

        // check whether this is a new file or an edit
        $new_file = empty($guid);
        $old_file_size = 0;

        if ($new_file) {
            if (empty($uploaded_file)) {
                return elgg_error_response(elgg_echo('file:uploadfailed'));
            }

            $file = new \ElggFile();
        } else {
            // load original file object
            $file = get_entity($guid);
            if (!$file instanceof \ElggFile) {
                return elgg_error_response(elgg_echo('file:cannotload'));
            }

            // user must be able to edit file
            if (!$file->canEdit()) {
                return elgg_error_response(elgg_echo('file:noaccess'));
            }

            $old_file_size = $file->getSize();
        }

        if (!elgg_is_empty($title)) {
            $file->title = $title;
        }
        $file->description = $desc;
        $file->access_id = $access_id;
        $file->container_guid = $container_guid;
        $file->tags = elgg_string_to_array($tags);

        $file->save();

        if ($uploaded_file && $uploaded_file->isValid()) {
            if (!$file->acceptUploadedFile($uploaded_file)) {
                return elgg_error_response(elgg_echo('file:uploadfailed'));
            }

            if (!$file->save()) {
                return elgg_error_response(elgg_echo('file:uploadfailed'));
            }

            // update disk quota
            if (!$new_file) {
                $disk_quota->update($old_file_size);
            }

            // set disk quota
            $file->diskspace_used = $disk_quota->getCurrentUploadSize();

            // remove old icons
			$file->deleteIcon();
			
			// update icons
			if ($file->getSimpleType() === 'image') {
				$file->saveIconFromElggFile($file);
			}
        }

        $forward = (string) $file->getURL();

        // handle results differently for new files and file updates
        if ($new_file) {
            $container = get_entity($container_guid);
            if ($container instanceof \ElggGroup) {
                $forward = elgg_generate_url('collection:object:file:group', ['guid' => (int) $container->guid]);
            } else {
                $forward = elgg_generate_url('collection:object:file:owner', ['username' => (string) $container->username]);
            }

            elgg_create_river_item([
                'action_type' => 'create',
                'object_guid' => (int) $file->guid,
            ]);
        }

        return elgg_ok_response('', elgg_echo('file:saved'), $forward);
    }
}
