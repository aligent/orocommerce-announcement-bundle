<?php
/**
 * @category  Aligent
 * @package
 * @author    Chris Rossi <chris.rossi@aligent.com.au>
 * @copyright 2022 Aligent Consulting.
 * @license
 * @link      http://www.aligent.com.au/
 */
namespace Aligent\AnnouncementBundle\Migrations\Schema\v1_0;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaException;
use Oro\Bundle\EntityConfigBundle\Entity\ConfigModel;
use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;
use Oro\Bundle\EntityExtendBundle\Migration\ExtendOptionsManager;
use Oro\Bundle\EntityExtendBundle\Migration\OroOptions;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 */
class CreateAnnouncementTables implements
    Migration
{
    /**
     * @throws SchemaException
     */
    public function up(Schema $schema, QueryBag $queries): void
    {
        /** Tables generation **/
        $this->createAligentAnnouncementTable($schema);
        $this->createAligentAnnouncementScheduleTable($schema);
        $this->createAligentAnnouncementScopeTable($schema);

        /** Foreign keys generation **/
        $this->addAligentAnnouncementForeignKeys($schema);
        $this->addAligentAnnouncementScheduleForeignKeys($schema);
        $this->addAligentAnnouncementScopeForeignKeys($schema);
    }

    protected function createAligentAnnouncementTable(Schema $schema): void
    {
        $table = $schema->createTable('aligent_announcement');

        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('organization_id', 'integer', ['notnull' => false]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('enabled', 'boolean', []);
        $table->addColumn('priority', 'integer', []);
        $table->addColumn('content', 'wysiwyg', [
            'notnull' => false,
            'length' => 0,
            'comment' => '(DC2Type:wysiwyg)',
        ]);
        $table->addColumn(
            'content_style',
            'wysiwyg_style',
            [
                'notnull' => false,
                OroOptions::KEY => [
                    ExtendOptionsManager::MODE_OPTION => ConfigModel::MODE_HIDDEN,
                    'extend' => ['is_extend' => true, 'owner' => ExtendScope::OWNER_SYSTEM],
                    'draft' => ['draftable' => false],
                ],
            ]
        );
        $table->addColumn(
            'content_properties',
            'wysiwyg_properties',
            [
                'notnull' => false,
                OroOptions::KEY => [
                    ExtendOptionsManager::MODE_OPTION => ConfigModel::MODE_HIDDEN,
                    'extend' => ['is_extend' => true, 'owner' => ExtendScope::OWNER_SYSTEM],
                    'draft' => ['draftable' => false],
                ],
            ]
        );
        $table->addColumn('created_at', 'datetime', ['length' => 0]);
        $table->addColumn('updated_at', 'datetime', ['length' => 0]);

        $table->setPrimaryKey(['id']);

        $table->addIndex(['enabled'], 'enabled_idx', []);
        $table->addIndex(['organization_id'], 'IDX_854CED1B32C8A3DE', []);
    }

    protected function createAligentAnnouncementScheduleTable(Schema $schema): void
    {
        $table = $schema->createTable('aligent_announcement_schedule');

        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('announcement_id', 'integer', ['notnull' => false]);
        $table->addColumn('active_at', 'datetime', ['notnull' => false, 'length' => 0]);
        $table->addColumn('deactivate_at', 'datetime', ['notnull' => false, 'length' => 0]);

        $table->setPrimaryKey(['id']);

        $table->addIndex(['announcement_id'], 'IDX_8F6E22E1913AEA17', []);
    }

    protected function createAligentAnnouncementScopeTable(Schema $schema): void
    {
        $table = $schema->createTable('aligent_announcement_scope');

        $table->addColumn('announcement_id', 'integer', []);
        $table->addColumn('scope_id', 'integer', []);

        $table->setPrimaryKey(['announcement_id', 'scope_id']);

        $table->addIndex(['scope_id'], 'IDX_5FE7697682B5931', []);
        $table->addIndex(['announcement_id'], 'IDX_5FE7697913AEA17', []);
    }

    /**
     * @throws SchemaException
     */
    protected function addAligentAnnouncementForeignKeys(Schema $schema): void
    {
        $table = $schema->getTable('aligent_announcement');

        $table->addForeignKeyConstraint(
            $schema->getTable('oro_organization'),
            ['organization_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => 'NO ACTION']
        );
    }

    /**
     * @throws SchemaException
     */
    protected function addAligentAnnouncementScheduleForeignKeys(Schema $schema): void
    {
        $table = $schema->getTable('aligent_announcement_schedule');

        $table->addForeignKeyConstraint(
            $schema->getTable('aligent_announcement'),
            ['announcement_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => 'NO ACTION']
        );
    }

    /**
     * @throws SchemaException
     */
    protected function addAligentAnnouncementScopeForeignKeys(Schema $schema): void
    {
        $table = $schema->getTable('aligent_announcement_scope');

        $table->addForeignKeyConstraint(
            $schema->getTable('aligent_announcement'),
            ['announcement_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => 'NO ACTION']
        );

        $table->addForeignKeyConstraint(
            $schema->getTable('oro_scope'),
            ['scope_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => 'NO ACTION']
        );
    }
}
