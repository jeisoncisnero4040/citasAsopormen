<?php

namespace App\Repositories;

use App\Interfaces\AuthsInterface;
use App\Models\Auth;
use App\Constanst\Auths;
use App\Dtos\GetAuthsDto;
use Illuminate\Support\Facades\DB;
use App\utils\ZerosPadder;
use App\Serializers\AuthsSerializer;
use App\Commands\AuthCommand;
use App\Exceptions\CustomExceptions\ServerErrorException;

class AuthsRepository extends BaseRepository implements AuthsInterface
{
    /**
     * @return array<Auth>
     */
    public function get(GetAuthsDto $dto): array
    {
        $filters = $this->buildFilters($dto);

        $response = $this->executeQuery(
            Auths::BASE_GET_AUTHS_QUERY,
            $filters
        );

        return collect($response)
            ->map(fn($item) => Auth::fromArray((array) $item))
            ->toArray();
    }
    public function getByIds(array $ids): array
    {
        if(empty($ids)){
            return [];
        }
        $placeholders = self::makePlaceholdersPlains($ids);
        $query = str_replace('{{}}', "AND id IN ($placeholders)", Auths::BASE_GET_AUTHS_QUERY);
        $response = self::sendQuery(
            query: $query,
            bindings: $ids
        );
        return collect($response)
            ->map(fn($item) => Auth::fromArray((array) $item))
            ->toArray();
    }
    /**
     * @param array<AuthCommand> $auths  
     * @return array<int>
     */
    public function saveMany(array $auths): array
    {
        DB::beginTransaction();
        try {
            $authExample = $auths[0];
            $exists = DB::select("SELECT 1 FROM autoriza 
                WHERE n_autoriza = ? AND entidad = ? AND historia = ?",
                [
                    $authExample->getAuthCode(),
                    $authExample->getEpsCode(),
                    $authExample->getClientCode()
                ]
            );
            if (!empty($exists)) {
                throw new ServerErrorException("La autorizacion que deseas ingresar ya existe",500);
            }
            $consecutives = DB::select("SELECT CONSECU FROM con_inv WITH (UPDLOCK, ROWLOCK) WHERE sigla = 'AU'");
            $lastId = DB::select("SELECT TOP 1 id FROM autoriza  ORDER BY id DESC")[0]->id ?? 0;

            $currentConsecutive = $consecutives[0]->CONSECU ?? '0000000000';
            $nextConsecutive = ZerosPadder::increment($currentConsecutive, 10);
            foreach ($auths as $auth) {
                $auth->setConsecutive($currentConsecutive);
            }
            $query = $this->buildCreateQuery(
                'autoriza',
                AuthsSerializer::toPersistence($authExample),
                count($auths)
            );
            $bindings = [];
            foreach ($auths as $auth) {
                $bindings = [
                    ...$bindings,
                    ...array_values(AuthsSerializer::toPersistence($auth))
                ];
            }

            DB::insert($query, $bindings);
            DB::update("UPDATE con_inv SET CONSECU = ? WHERE sigla = 'AU'", [$nextConsecutive]);
            DB::commit();
            return range($lastId + 1, $lastId + count($auths));

        } catch (ServerErrorException $e) {
            DB::rollBack();
            throw $e;
        }
        catch(\Exception){
            DB::rollBack();
            throw new ServerErrorException("Ha ocurrido un error al intentar guardar la autorizacion, por favor intenta de nuevo",500);
        }
    }

    private function buildFilters(GetAuthsDto $dto): Filter
    {
        $builder = new FilterBuilder();

        $builder ->addRaw("(a.anulada = 0 OR (a.suspendida = 1 AND a.anulada = 1))");
        if ($dto->hasAuthCode()) {
            $builder->add("a.n_autoriza = ?", $dto->getAuthCode());
            return $builder->toFilter();

        }

        if ($dto->isOnlySchedulables()) {
            $builder
                ->add("a.historia = ?", $dto->getClientCode())
                ->addRaw("a.f_vence >= CAST(GETDATE() AS DATE)")
                ->addRaw("a.f_inicial <= CAST(GETDATE() AS DATE)")
                ->addRaw("a.cerrar_ord_asp <> '1'")
                ->addRaw("
                    NOT EXISTS (
                        SELECT 1
                        FROM ven_det vd
                        WHERE vd.autoriz = a.n_autoriza
                        AND vd.codigo = a.historia
                        AND vd.abierta = '0'
                        AND vd.detalle = ''
                    )
                ");
            return $builder->toFilter();
        }
        if($dto->hasUserCode()){
            $builder->add("a.historia = ?", $dto->getClientCode());

        }



        if ($dto->hasDateRange()) {
            $builder
                ->add("a.f_inicial >= CONVERT(smalldatetime, ?, 120)", $dto->getFrom())
                ->add("a.f_vence <= CONVERT(smalldatetime, ?, 120)", $dto->getTo());
        }

        return $builder->toFilter();
    }

    public function getDetailAuth(GetAuthsDto $dto): array
    {
        return $this->executeQueryWithBuilder(
            Auths::TEMPLATE_GET_TRAZABILITY,
            FilterBuilder::create()
                ->add('a.historia = ?', $dto->getClientCode())
                ->add('a.n_autoriza = ?', $dto->getAuthCode())
        );
    }

    public function getDetailApposAppos(GetAuthsDto $dto): array
    {
        return $this->executeQueryWithBuilder(
            Auths::TEMPLATE_GET_INFO_APPOS_AUTH,
            FilterBuilder::create()
                ->add('ci.nro_hist = ?', $dto->getClientCode())
                ->add('ci.autoriz = ?', $dto->getAuthCode())
        );
    }

    public function getDetailsOrders(GetAuthsDto $dto): array
    {
        return $this->executeQueryWithBuilder(
            Auths::TEMPLATE_GET_TRAZA_ORDERS,
            FilterBuilder::create()
                ->add('a.historia = ?', $dto->getClientCode())
                ->add('a.n_autoriza = ?', $dto->getAuthCode())
        );
    }


    private function executeQuery(string $template, Filter $filter): array
    {
        $query = str_replace('{{}}', $filter->getQuery(), $template);
        return self::sendQuery(
            query: $query,
            bindings: $filter->getBindings()
        );
    }


    private function executeQueryWithBuilder(string $template, FilterBuilder $builder): array
    {
        $filter = $builder->toFilter();

        return $this->executeQuery($template, $filter);
    }
}