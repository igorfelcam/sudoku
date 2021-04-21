<?php

namespace Services;

class Search
{
    /**
     * Get first empty item of parent node
     *
     * @param array $parentNode
     * @return array
     */
    public function getFirstEmptyItem($parentNode)
    {
        $line_id    = null;
        $section_id = null;
        $item_id    = null;

        // walk line
        foreach ($parentNode as $line_key => $line) {
            $line_id = $line_key;

            // walk section
            foreach ($line as $section_key => $section) {
                $section_id = $section_key;

                // walk item
                foreach ($section as $item_key => $item) {
                    if ($item === "_") {
                        $item_id = $item_key;
                        break;
                    }
                }

                if ($item_id !== null) {
                    break;
                }
            }

            if ($item_id !== null) {
                break;
            }
        }

        return [
            $line_id,
            $section_id,
            $item_id
        ];
    }
}
