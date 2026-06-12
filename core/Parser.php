<?php
/**
 * ZimsecExamMate — Filename Parser
 * 
 * Format: SUBJECTCODE_YEAR_RESOURCETYPE_SUBTYPE.pdf
 * 
 * Examples:
 *   4004_2024_PP_PAPER2.pdf    → Mathematics 2024 Past Paper 2
 *   4025_2024_NT_REVISION.pdf  → Biology 2024 Notes (Revision Guide)
 *   4023_2024_TP_TOPIC3.pdf    → Chemistry 2024 Topical Topic 3
 */

class Parser
{
    /**
     * Parse a filename into structured metadata
     */
    public static function parseFilename(string $filename): array
    {
        $result = [
            'subject_code'        => '',
            'subject_name'        => 'Unknown Subject',
            'year'                => 'N/A',
            'resource_type'       => 'unknown',
            'resource_type_code'  => '',
            'resource_type_display' => 'Unknown',
            'type_directory'      => '',
            'subtype'             => '',
            'subtype_display'     => '',
            'paper_number'        => null,
            'paper_display'       => '',
            'topic_number'        => null,
            'topic_display'       => '',
            'additional_info'     => '',
            'is_parsed'           => false,
        ];

        // Remove extension
        $nameWithoutExt = preg_replace('/\.pdf$/i', '', $filename);
        
        // Split by underscore
        $parts = explode('_', $nameWithoutExt);
        
        // Need at least: CODE_YEAR_TYPE
        if (count($parts) < 3) {
            return $result;
        }

        // Part 0: Subject code (4 digits)
        $subjectCode = trim($parts[0]);
        if (!preg_match('/^\d{4}$/', $subjectCode)) {
            return $result;
        }
        $result['subject_code'] = $subjectCode;
        $result['subject_name'] = Config::getSubjectName($subjectCode);

        // Part 1: Year (4 digits)
        $year = trim($parts[1]);
        if (preg_match('/^\d{4}$/', $year)) {
            $result['year'] = $year;
        }

        // Part 2: Resource type code
        $typeCode = strtoupper(trim($parts[2]));
        $result['resource_type_code'] = $typeCode;

        if (isset(RESOURCE_TYPES[$typeCode])) {
            $mappedType = RESOURCE_TYPES[$typeCode];
            $result['resource_type'] = $mappedType;
            $result['resource_type_display'] = RESOURCE_TYPE_DISPLAY[$mappedType] ?? $typeCode;
            $result['type_directory'] = TYPE_DIR_MAP[$typeCode] ?? '';
        }

        // Part 3+: Subtype / Additional info
        if (count($parts) >= 4) {
            $subtype = strtoupper(trim($parts[3]));
            $result['subtype'] = $subtype;
            $result['additional_info'] = $subtype;

            // Parse subtype based on resource type
            switch ($typeCode) {
                case 'PP':
                case 'MS':
                    // PAPER1, PAPER2, PAPER3, PAPER4, COMBINED
                    if (preg_match('/^PAPER(\d+)$/', $subtype, $m)) {
                        $result['paper_number'] = (int) $m[1];
                        $result['paper_display'] = "Paper {$m[1]}";
                        $result['subtype_display'] = "Paper {$m[1]}";
                    } elseif ($subtype === 'COMBINED') {
                        $result['paper_display'] = 'Combined Papers';
                        $result['subtype_display'] = 'Combined';
                    }
                    break;

                case 'TP':
                    // TOPIC1, TOPIC2, etc.
                    if (preg_match('/^TOPIC(\d+)$/', $subtype, $m)) {
                        $result['topic_number'] = (int) $m[1];
                        $result['paper_number'] = (int) $m[1];
                        $result['topic_display'] = "Topic {$m[1]}";
                        $result['subtype_display'] = "Topic {$m[1]}";
                    }
                    break;

                case 'NT':
                    // NOTES, TEXTBOOK, GUIDE, SUMMARY, REVISION, WORKBOOK, COMBINED
                    $ntLabels = [
                        'NOTES'    => 'Study Notes',
                        'TEXTBOOK' => 'Textbook',
                        'GUIDE'    => 'Study Guide',
                        'SUMMARY'  => 'Summary Notes',
                        'REVISION' => 'Revision Guide',
                        'WORKBOOK' => 'Workbook',
                        'COMBINED' => 'Combined',
                    ];
                    $result['subtype_display'] = $ntLabels[$subtype] ?? ucwords(strtolower($subtype));
                    $result['paper_display'] = $result['subtype_display'];
                    break;

                case 'SY':
                    $result['subtype_display'] = 'Syllabus';
                    $result['paper_display'] = 'Syllabus';
                    break;

                case 'PR':
                    // GUIDE, SAMPLE, PROJECT, TEMPLATE, REPORT
                    $prLabels = [
                        'GUIDE'    => 'Project Guide',
                        'SAMPLE'   => 'Sample Project',
                        'PROJECT'  => 'Project',
                        'TEMPLATE' => 'Template',
                        'REPORT'   => 'Project Report',
                    ];
                    $result['subtype_display'] = $prLabels[$subtype] ?? ucwords(strtolower($subtype));
                    $result['paper_display'] = $result['subtype_display'];
                    break;
            }

            // Part 4+: Extra info (ignore or append)
            if (count($parts) >= 5) {
                $extra = strtoupper(trim($parts[4]));
                $result['additional_info'] .= '_' . $extra;
            }
        }

        $result['is_parsed'] = true;
        return $result;
    }

    /**
     * Generate a display title
     */
    public static function generateTitle(array $fileData): string
    {
        $parts = [];
        $parts[] = $fileData['subject_name'] ?? 'Unknown';
        
        if (!empty($fileData['paper_display'])) {
            $parts[] = $fileData['paper_display'];
        } elseif (!empty($fileData['subtype_display'])) {
            $parts[] = $fileData['subtype_display'];
        }
        
        $parts[] = $fileData['resource_type_display'] ?? 'Resource';
        
        if (!empty($fileData['year']) && $fileData['year'] !== 'N/A') {
            $parts[] = '(' . $fileData['year'] . ')';
        }

        return implode(' — ', $parts);
    }

    /**
     * Generate filename following convention
     */
    public static function generateFilename(
        string $subjectCode, 
        string $year, 
        string $resourceType, 
        string $subtype = ''
    ): string {
        $name = $subjectCode . '_' . $year . '_' . $resourceType;
        
        if (!empty($subtype)) {
            $clean = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '_', $subtype));
            $name .= '_' . $clean;
        }
        
        return $name . '.pdf';
    }
}