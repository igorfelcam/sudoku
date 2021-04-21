<?php

namespace Services;

class Search
{
    /**
     * Get empty items of parent node
     *
     * @param array $parentNode
     * @return array $emptyItems
     */
    public function getEmptyItems($parentNode)
    {
        $emptyItems = [];

        // walk line
        foreach ($parentNode as $line_key => $line) {
            // walk section
            foreach ($line as $section_key => $section) {
                // walk item
                foreach ($section as $item_key => $item) {
                    if ($item === "_") {
                        $emptyItems[] = [
                            "line"      => $line_key,
                            "section"   => $section_key,
                            "item"      => $item_key
                        ];
                    }
                }
            }
        }

        return $emptyItems;
    }
}
