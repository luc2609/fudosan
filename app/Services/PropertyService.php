<?php

namespace App\Services;

use App\Exports\PropertyExport;

use App\Repositories\MasterRail\MasterRailRepositoryInterface;
use App\Repositories\Project\ProjectRepositoryInterface;
use App\Repositories\Property\PropertyRepositoryInterface;
use App\Repositories\PropertyCustomValue\PropertyCustomValueRepositoryInterface;
use App\Repositories\PropertyFile\PropertyFileRepositoryInterface;
use App\Repositories\PropertyStation\PropertyStationRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use DateTime;

class PropertyService
{
    protected $propertyInterface;
    protected $fileService;
    protected $propertyStationInterface;
    protected $userInterface;
    protected $masterRailInterface;
    protected $propertyFileInterface;
    protected $projectInterface;
    protected $propertyCustomValueInterface;

    public function __construct(
        PropertyRepositoryInterface $propertyInterface,
        PropertyStationRepositoryInterface $propertyStationInterface,
        FileService $fileService,
        UserRepositoryInterface $userInterface,
        MasterRailRepositoryInterface $masterRailInterface,
        PropertyFileRepositoryInterface $propertyFileInterface,
        ProjectRepositoryInterface $projectInterface,
        PropertyCustomValueRepositoryInterface $propertyCustomValueInterface
    ) {
        $this->propertyInterface = $propertyInterface;
        $this->propertyStationInterface = $propertyStationInterface;
        $this->fileService = $fileService;
        $this->userInterface = $userInterface;
        $this->masterRailInterface = $masterRailInterface;
        $this->propertyFileInterface = $propertyFileInterface;
        $this->projectInterface = $projectInterface;
        $this->propertyCustomValueInterface = $propertyCustomValueInterface;
    }

    // List Property in company
    public function list($params)
    {
        $companyId = auth()->user()->company;

        $pageSize = $params['page_size'] ?? PAGE_SIZE;
        $properties = $this->propertyInterface->listInCompany($companyId, $params)->paginate($pageSize);

        $data = [
            'properties' => $properties->items(),
            'items_total' => $properties->total()
        ];

        return _success($data, __('message.property_list_success'), HTTP_SUCCESS);
    }

    // Export list Property in company
    public function export($params)
    {
        $companyId = auth()->user()->company;
        $properties = $this->propertyInterface->listInCompany($companyId, $params)->get();
        $currentDate = date('Ymd');
        $userId = auth()->user()->id;
        $fileName = $currentDate . __('filename.export_property');
        $filePath = 'property/' . $userId . '/' . $fileName;
        $exportedObject = new PropertyExport($properties);
        $link = $this->fileService->saveFileToS3($filePath, $exportedObject);
        $data = ['link' => $link];

        return _success($data, __('message.export_success'), HTTP_SUCCESS);
    }

    // Check duplicate property
    public function checkDuplicate($params, $propertyId)
    {
        $authUser = auth()->user();
        $companyId = $authUser->company;

        // check duplicate rail station
        if (isset($params['rail_stations'])) {
            $railStations = $params['rail_stations'];

            $masterStationIds = [];
            foreach ($railStations as $railStation) {
                if (is_string($railStation)) {
                    $railStation = json_decode($railStation, true);
                }

                $railCd = $railStation['rail_cd'] ?? '';
                $stationCd = $railStation['station_cd'] ?? '';
                $masterStation = $this->masterRailInterface->getMasterStation($railCd, $stationCd);

                if ($masterStation) {
                    if (in_array($masterStation->id, $masterStationIds)) {
                        return _error(null, __('message.duplicate_rail_station'), HTTP_BAD_REQUEST);
                    } else {
                        $masterStationIds[] = $masterStation->id;
                    }
                }
            }
        }

        $attributesCheckDuplicate = [
            'name' => $params['name'] ?? '',
            'postal_code' => $params['postal_code'] ?? '',
            'address' => $params['address'] ?? '',
            'properties_type_id' => $params['properties_type_id'] ?? '',
            'status' => APPROVED_PROPERTY,
            'company' => $companyId
        ];

        $listPropertyCheckDuplicate = $this->propertyInterface->getByAttributes($attributesCheckDuplicate);

        if ($listPropertyCheckDuplicate->count() > 0) {
            if ($listPropertyCheckDuplicate[0]->id == $propertyId) {
                return false;
            }

            $data = [
                'id' => $listPropertyCheckDuplicate[0]->id
            ];

            return _error($data, __('message.duplicate_property'), HTTP_BAD_REQUEST);
        }

        return false;
    }

    // Create property
    public function create($params)
    {
        $constructionDate = isset($params['construction_date']) ? new DateTime($params['construction_date']) : null;
        $authUser = auth()->user();
        $status = APPROVED_PROPERTY;
        $createdId = $authUser->id;
        $createdName = $authUser->username;
        $companyId = $authUser->company;

        // create property
        $attributes = [
            'avatar' => null,
            'name' => $params['name'] ?? '',
            'construction_date' =>  $constructionDate,
            'postal_code' => $params['postal_code'] ?? '',
            'province' => $params['province'] ?? '',
            'district' => $params['district'] ?? '',
            'address' => $params['address'] ?? '',
            'price' => $params['price'] ?? 0,
            'contract_type_id' => $params['contract_type_id'] ?? null,
            'properties_type_id' => $params['properties_type_id'] ?? null,
            'land_area' => $params['land_area'] ?? null,
            'total_floor_area' => $params['total_floor_area'] ?? null,
            'usage_ratio' => $params['usage_ratio'] ?? null,
            'empty_ratio' => $params['empty_ratio'] ?? null,
            'floor' => $params['floor'] ?? null,
            'building_structure_id' => $params['building_structure_id'] ?? null,
            'design' => $params['design'] ?? null,
            'description' => $params['description'] ?? null,
            'status' => $status,
            'created_id' => $createdId,
            'created_name' => $createdName,
            'company_id' => $companyId
        ];

        $property = $this->propertyInterface->create($attributes);
        $propertyId = $property->id;

        // avatar
        if (isset($params['avatar'])) {
            // TODO: Viết thành 1 hàm upload chung
            $file = $params['avatar'];
            $filePath = 'property/' . $propertyId . '/avatar';
            $avatarUrl = $this->fileService->uploadFileToS3($file, $filePath);

            $attributesUpdate = ['avatar' => $avatarUrl];

            $this->propertyInterface->update($propertyId, $attributesUpdate);
        }

        // images
        if (isset($params['images'])) {
            // TODO: Viết thành 1 hàm upload chung
            $files = $params['images'];
            $filePath = 'property/' . $propertyId . '/images';

            foreach ($files as $file) {
                $fileUrl = $this->fileService->uploadFileToS3($file, $filePath);

                $this->propertyFileInterface->create([
                    'property_id' => $propertyId,
                    'name' => $file->getClientOriginalName(),
                    'url'  => $fileUrl,
                    'type' => IMAGE_FILE_TYPE
                ]);
            }
        }

        // documents
        if (isset($params['documents'])) {
            // TODO: Viết thành 1 hàm upload chung
            $files = $params['documents'];
            $filePath = 'property/' . $propertyId . '/documents';

            foreach ($files as $file) {
                $fileUrl = $this->fileService->uploadFileToS3($file, $filePath);

                $this->propertyFileInterface->create([
                    'property_id' => $propertyId,
                    'name' => $file->getClientOriginalName(),
                    'url'  => $fileUrl,
                    'type' => DOCUMENT_FILE_TYPE
                ]);
            }
        }

        // rail station
        if (isset($params['rail_stations'])) {
            $railStations = [];
            foreach ($params['rail_stations'] as $railStation) {
                if (is_string($railStation)) {
                    $railStation = json_decode($railStation, true);
                }

                array_push($railStations, [
                    'property_id' => $propertyId,
                    'rail_cd' => $railStation['rail_cd'],
                    'station_cd' => $railStation['station_cd'],
                    'on_foot' => isset($railStation['on_foot']) ? (int) $railStation['on_foot'] : null
                ]);
            }

            $this->propertyStationInterface->insert($railStations);
        }

        // custom fields
        $customFieldParams = [];
        if (isset($params['custom_fields'])) {
            $customFieldParams = $params['custom_fields'];
        }
        foreach ($customFieldParams  as $customField) {
            if (is_string($customField)) {
                $customField = json_decode($customField, true);
            }
            $params = [
                'custom_field_id' => $customField['custom_field_id'],
                'value' => $customField['value'],
                'property_id' => $propertyId
            ];
            $this->propertyCustomValueInterface->create($params);
        }

        return [
            'id' => $propertyId
        ];
    }

    // Update property
    public function update($propertyId, $params)
    {
        $property = $this->propertyInterface->find($propertyId);

        // list attribute
        $constructionDate = isset($params['construction_date']) ? new DateTime($params['construction_date']) : null;

        $attributes = [
            'name' => $params['name'] ?? '',
            'construction_date' =>  $constructionDate,
            'postal_code' => $params['postal_code'] ?? '',
            'province' => $params['province'] ?? '',
            'district' => $params['district'] ?? '',
            'address' => $params['address'] ?? '',
            'price' => $params['price'] ?? 0,
            'contract_type_id' => $params['contract_type_id'] ?? null,
            'properties_type_id' => $params['properties_type_id'] ?? null,
            'land_area' => $params['land_area'] ?? null,
            'total_floor_area' => $params['total_floor_area'] ?? null,
            'usage_ratio' => $params['usage_ratio'] ?? null,
            'empty_ratio' => $params['empty_ratio'] ?? null,
            'floor' => $params['floor'] ?? null,
            'building_structure_id' => $params['building_structure_id'] ?? null,
            'design' => $params['design'] ?? null,
            'description' => $params['description'] ?? null,
        ];

        // delete_avatar
        if (isset($params['delete_avatar'])) {
            $deleteAvatar = $params['delete_avatar'];

            $currentAvatarUrl = $property->avatar;

            if ($deleteAvatar && $currentAvatarUrl) {
                $this->fileService->deleteFileS3($currentAvatarUrl);
                $attributes['avatar'] = null;
            }
        }

        // avatar
        if (isset($params['avatar'])) {
            $file = $params['avatar'];
            $filePath = 'property/' . $propertyId . '/avatar';
            $avatarUrl = $this->fileService->uploadFileToS3($file, $filePath);
            $currentAvatarUrl = $property->avatar;

            if ($currentAvatarUrl) {
                $this->fileService->deleteFileS3($currentAvatarUrl);
            }

            $attributes['avatar'] = $avatarUrl;
        }

        // delete images
        if (isset($params['delete_image_ids'])) {
            $deleteImageIds = $params['delete_image_ids'];

            foreach ($deleteImageIds as $fileId) {
                $file = $this->propertyFileInterface->find($fileId);

                if ($file) {
                    $this->fileService->deleteFileS3($file->url);
                    $this->propertyFileInterface->delete($fileId);
                }
            }
        }

        // images
        if (isset($params['images'])) {
            $files = $params['images'];
            $filePath = 'property/' . $propertyId . '/images';

            foreach ($files as $file) {
                $fileUrl = $this->fileService->uploadFileToS3($file, $filePath);

                $this->propertyFileInterface->create([
                    'property_id' => $propertyId,
                    'name' => $file->getClientOriginalName(),
                    'url'  => $fileUrl,
                    'type' => IMAGE_FILE_TYPE
                ]);
            }
        }

        // delete documents
        if (isset($params['delete_document_ids'])) {
            $deleteDocumentIds = $params['delete_document_ids'];

            foreach ($deleteDocumentIds as $fileId) {
                $file = $this->propertyFileInterface->find($fileId);

                if ($file) {
                    $this->fileService->deleteFileS3($file->url);
                    $this->propertyFileInterface->delete($fileId);
                }
            }
        }

        // documents
        if (isset($params['documents'])) {
            $files = $params['documents'];
            $filePath = 'property/' . $propertyId . '/documents';

            foreach ($files as $file) {
                $fileUrl = $this->fileService->uploadFileToS3($file, $filePath);

                $this->propertyFileInterface->create([
                    'property_id' => $propertyId,
                    'name' => $file->getClientOriginalName(),
                    'url'  => $fileUrl,
                    'type' => DOCUMENT_FILE_TYPE
                ]);
            }
        }

        // rail station
        $railStationParams = [];
        if (isset($params['rail_stations'])) {
            $railStationParams = $params['rail_stations'];
        }
        $currentPropertyStationIds = $property->propertyStations->pluck('id')->toArray();
        $updatePropertyStationIds = [];

        // create and update property station
        foreach ($railStationParams as $railStation) {
            if (is_string($railStation)) {
                $railStation = json_decode($railStation, true);
            }

            $propertyStation = $property->propertyStations
                ->where('rail_cd', $railStation['rail_cd'])
                ->where('station_cd', $railStation['station_cd'])
                ->first();

            if ($propertyStation) {
                $this->propertyStationInterface->update($propertyStation->id, [
                    'on_foot' => isset($railStation['on_foot']) ? (int) $railStation['on_foot'] : null
                ]);

                array_push($updatePropertyStationIds, $propertyStation->id);
            } else {
                $this->propertyStationInterface->create([
                    'property_id' => $propertyId,
                    'rail_cd' => $railStation['rail_cd'],
                    'station_cd' => $railStation['station_cd'],
                    'on_foot' => isset($railStation['on_foot']) ? (int) $railStation['on_foot'] : null
                ]);
            }
        }

        $deletePropertyStationIds = array_diff($currentPropertyStationIds, $updatePropertyStationIds);
        foreach ($deletePropertyStationIds as $deletePropertyStationId) {
            $this->propertyStationInterface->delete($deletePropertyStationId);
        }

        // update custom field
        $customFieldParams = [];
        if (isset($params['custom_fields'])) {
            $customFieldParams = $params['custom_fields'];
        }
        foreach ($customFieldParams as $customField) {
            if (is_string($customField)) {
                $customField = json_decode($customField, true);
            }
            if (isset($customField['id'])) {
                $this->propertyCustomValueInterface->update(
                    $customField['id'],
                    [
                        'value' => $customField['value']
                    ]
                );
            } else {
                $params = [
                    'custom_field_id' => $customField['custom_field_id'],
                    'value' => $customField['value'],
                    'property_id' => $propertyId
                ];
                $this->propertyCustomValueInterface->create($params);
            }
        }

        $this->propertyInterface->update($propertyId, $attributes);

        return [
            'id' => $propertyId
        ];
    }

    // Show Brokerage Fee
    public function showBrokerageFee($params)
    {
        if (!isset($params['price'])) {
            return 0;
        }

        $price = $params['price'];

        if ($price < 2000000) {
            $brokerageFee = $price * 0.05;
        } else if ($price <= 4000000) {
            $brokerageFee = $price * 0.04 + 20000;
        } else {
            $brokerageFee = $price * 0.03 + 60000;
        }

        $tax = $brokerageFee * 0.1;
        $brokerageFee = $brokerageFee + $tax;
        $brokerageFee = round($brokerageFee);

        $data = [
            'brokerage_fee' => $brokerageFee
        ];

        return _success($data, __('message.get_success'), HTTP_SUCCESS);
    }

    // Delete propety
    public function delete($id)
    {
        $checkProperty = $this->checkProperty($id);
        if ($checkProperty) {
            return $checkProperty;
        }

        $checkPropertyInProject = $this->checkPropertyInProject($id);
        if ($checkPropertyInProject) {
            return $checkPropertyInProject;
        }
        $property = $this->propertyInterface->find($id);
        $property->propertyCustomValues()->delete();
        $this->propertyInterface->delete($id);

        return _success(null, __('message.deleted_success'), HTTP_SUCCESS);
    }

    // Show propety
    public function show($request, $id)
    {
        $checkProperty = $this->checkProperty($id);
        if ($checkProperty) {
            return $checkProperty;
        }
        $params = $request->all();
        $companyId = auth()->user()->company;
        $property = $this->propertyInterface->get($params, $id, $companyId);

        return _success($property, __('message.show_success'), HTTP_SUCCESS);
    }

    public function createPropertyStatus($authUser)
    {
        if ($authUser->hasRole(ADMIN_CMS_COMPANY_ROLE) || $authUser->hasRole(MANAGER_ROLE)) {
            return APPROVED_PROPERTY;
        }

        return WAIT_PROPERTY;
    }

    // Check property
    public function checkProperty($id)
    {
        $userId = auth()->user()->id;
        $user = $this->userInterface->find($userId);

        $company = $user->company()->first();
        if (!$company) {
            return _error(null, __('message.not_found'), HTTP_BAD_REQUEST);
        }

        $property = $this->propertyInterface->find($id);

        if (!$property) {
            return _error(null, __('message.property_not_found'), HTTP_NOT_FOUND);
        } else if ($property->company_id != $company->id) {
            return _error(null, __('message.no_permission'), HTTP_FORBIDDEN);
        }

        return false;
    }

    // Check files
    public function checkFiles($id, $params)
    {
        $property = $this->propertyInterface->find($id);

        // Check image count
        $imagesCurrentCount = count($property->images);
        $imagesDeleteCount = 0;

        if (isset($params['delete_image_ids'])) {
            $deleteImageIds = $params['delete_image_ids'];

            foreach ($deleteImageIds as $deleteImageId) {
                if (!$property->images->contains('id', $deleteImageId)) {
                    return _error(null, __('message.delete_image_id_not_correct'), HTTP_BAD_REQUEST);
                }
            }

            $imagesDeleteCount = count($deleteImageIds);
        }

        if (isset($params['images'])) {
            $imagesAddCount = count($params['images']);

            if ($imagesCurrentCount - $imagesDeleteCount + $imagesAddCount > 10) {
                return _error('false', __('message.image_over_limited'), HTTP_BAD_REQUEST);
            }
        }

        // Check document count
        $documentsCurrentCount = count($property->documents);
        $documentsDeleteCount = 0;

        if (isset($params['delete_document_ids'])) {
            $deleteDocumentIds = $params['delete_document_ids'];

            foreach ($deleteDocumentIds as $deleteDocumentId) {
                if (!$property->documents->contains('id', $deleteDocumentId)) {
                    return _error(null, __('message.delete_document_id_not_correct'), HTTP_BAD_REQUEST);
                }
            }

            $documentsDeleteCount = count($deleteDocumentIds);
        }

        if (isset($params['documents'])) {
            $documentsAddCount = count($params['documents']);

            if ($documentsCurrentCount - $documentsDeleteCount + $documentsAddCount > 5) {
                return _error('false', __('message.document_over_limited'), HTTP_BAD_REQUEST);
            }
        }

        return false;
    }

    // Check property exist in the project
    public function checkPropertyInProject($id)
    {
        $property = $this->propertyInterface->find($id);
        $existProject = $property->projects()->exists();
        if ($existProject) {
            return _error(null, __('message.not_delete_property'), HTTP_BAD_REQUEST);
        }
        return false;
    }

    // Project property
    public function indexProject($request, $id)
    {
        $checkProperty = $this->checkProperty($id);
        if ($checkProperty) {
            return $checkProperty;
        }

        $pageSize = $request->page_size ?? PAGE_SIZE;
        $countProjectProperty = $this->projectInterface->countProjectProperty($id);
        $listProjectProperties = $this->projectInterface->listProjectProperty($request, $id)->paginate($pageSize);

        $data = [
            'object_name' => $this->propertyInterface->find($id)->name,
            'quantity_project' =>  $countProjectProperty,
            'projects' =>  $listProjectProperties->items(),
            'items_total' => $listProjectProperties->total(),
            'current_page' => $request->page,
        ];

        return _success($data, __('success'), HTTP_SUCCESS);
    }
}
