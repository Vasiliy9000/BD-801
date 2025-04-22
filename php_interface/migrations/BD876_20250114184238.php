<?php

namespace Sprint\Migration;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Cargonomica\Service\ConstantsService;
use Cargonomica\Service\Migration\StageWorkflowTemplateService;
use Cargonomica\Service\Migration\WorkflowEntityType;
use Sprint\Migration\Module;
use Exception;

class BD876_20250114184238 extends Version
{

    protected $description = "Миграция на логику назначения ответственного в шаблоне БП стадии лида \"Назначен ответственный - Cargo Fuel\"";

    protected $moduleVersion = "4.15.1";

    protected WorkflowEntityType $entityType;

    public function __construct()
    {
        $this->entityType = WorkflowEntityType::lead();
    }

    /**
     * @return bool
     */
    public function up(): bool
    {
        return $this->updateStageWorkflowTemplates();
    }

    /**
     * @param bool $isNew
     * @return bool
     */
    protected function updateStageWorkflowTemplates(
        bool $isNew = true,
    ): bool
    {
        try {
            StageWorkflowTemplateService::updateStageWorkflowTemplates(
                static::getPrepareStages(),
                $this->getClassName(),
                $this->entityType,
                $isNew,
            );
        } catch (Exception $exception) {
            $this->outError($exception->getMessage());
            return false;
        }
        return true;
    }

    /**
     * Возвращает массив стадий сделки, для которых необходимо заменить роботы.
     *
     * @return array
     * @throws ArgumentException
     * @throws LoaderException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    protected static function getPrepareStages(): array
    {
        ConstantsService::define(true);
        return [
            'RESPONSIBLE_APPOINTED_FUEL' => RESPONSIBLE_APPOINTED_FUEL_LS_ID,
        ];
    }

    /**
     * @return bool
     */
    public function down(): bool
    {
        return $this->updateStageWorkflowTemplates(false);
    }
}
