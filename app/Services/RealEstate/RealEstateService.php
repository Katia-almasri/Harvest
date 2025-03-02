<?php
namespace App\Services\RealEstate;
use App\Enums\Media\MediaCollectionType;
use App\Enums\RealEstate\RealEstateStatus;
use App\Helpers\StringHelper;
use App\Models\RealEstate\RealEstate;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class RealEstateService{

    public function __construct(private MediaService $mediaService){}

    public function show(RealEstate $realEstate){
        return $realEstate;
    }

    public function store($data){
        try {
            DB::beginTransaction();
            $realEstate =  new RealEstate();
            $realEstate->unique_number = StringHelper::generateRandomString(15);
            $realEstate->fill($data);
            $realEstate->created_by = auth()->user()->id;
            $realEstate->save();
            DB::commit();
            return $realEstate;
        }catch (QueryException $e){
            DB::rollBack();
            return null;
        }
    }

    public function update($data, RealEstate $realEstate){
        try {
            $realEstate->update($data);
            return $realEstate->refresh();
        }catch (QueryException $e){
            return null;
        }
    }

    public function delete(RealEstate $realEstate){
            $status = $realEstate->status;
            switch ($status) {
                case RealEstateStatus::INACTIVE:
                case RealEstateStatus::SOLD:
                case RealEstateStatus::CLOSED:
                    $realEstate->delete();
                    break;
                default:
                    throw new \Exception("Unsupported status change");
                    break;
            }
        return $realEstate;
    }

    public function createMedia($model, $file, $mediaCollectionType=MediaCollectionType::REAL_ESTATE_IMAGE){
        return $this->mediaService->create($model, $file, $mediaCollectionType);
    }

    public function indexMedias(RealEstate $realEstate, $mediaCollectionType=MediaCollectionType::REAL_ESTATE_IMAGE){
       return $this->mediaService->index($realEstate, $mediaCollectionType);
    }

    public function showMedia(RealEstate $realEstate, $mediaId){
        return $this->mediaService->show($realEstate, $mediaId);
    }

    public function deleteMedia(RealEstate $realEstate, $media){
        $deletedMedia = $this->mediaService->delete($realEstate, $media);
        return $deletedMedia;
    }
}
